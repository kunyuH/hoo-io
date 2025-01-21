<?php

namespace hoo\io\common\Services;

use hoo\io\common\Exceptions\HooException;
use Illuminate\Support\Facades\Log;

/**
 * // 示例用法
 * try {
 * $logDir = '/var/log'; // 日志目录路径
 * $searcher = new DirectoryLogSearcher($logDir);
 *
 * // 搜索关键词 "error"，分页参数为每页5条，从第10条开始，限制文件扩展名为 ".log"
 * $keyword = 'error';
 * $limit = 5;
 * $offset = 10;
 * $fileExtension = 'log'; // 可选，指定日志文件后缀
 *
 * $results = $searcher->search($keyword, $limit, $offset, $fileExtension);
 *
 * // 输出搜索结果
 * foreach ($results as $line) {
 * echo $line . PHP_EOL. PHP_EOL;
 * }
 * } catch (Exception $e) {
 * echo "错误: " . $e->getMessage();
 * }
 */
class LogSearch extends BaseService
{
    private $logDir;        // 日志目录
    private $fileExtension; // 指定日志文件扩展名.可选.默认搜所有文件


    private $keyword;       // 搜索词
    private $limit;         // 每页显示条数
    private $offset;        // 起始行号

    public function __construct($logDir,$fileExtension='')
    {
//        if (!is_dir($logDir)) {
//            throw new HooException("日志目录不存在: $logDir");
//        }

        $this->logDir = $logDir;
        $this->fileExtension = $fileExtension;
    }

    public function where($keyword)
    {
        $this->keyword = $keyword;
        return $this;
    }

    public function limit($limit = 10, $offset = 0)
    {
        $this->limit = $limit;
        $this->offset = $offset;
        return $this;
    }

    /**
     * 获取日志文件内容
     * @return array
     * @throws HooException
     */
    public function get()
    {
        if(empty($this->keyword)){
            $escapedKeyword = "''";
        }else{
            // 确保关键词和目录路径是安全的.避免命令注入
            $escapedKeyword = escapeshellarg($this->keyword);
        }
        $escapedLogDir = escapeshellarg($this->logDir);

        // 如果指定扩展名.限制只搜索特定类型文件
        $findCommand = "find $escapedLogDir -type f";
        if (!empty($this->fileExtension)) {
            $escapedExtension = escapeshellarg("*.$this->fileExtension");
            $findCommand .= " -name $escapedExtension";
        }
        $grep = "grep -in {$escapedKeyword}";
        # true 没有搜索词
        if($escapedKeyword == "''"){
            // true 是目录 则显示内容所在的具体文件
            if(is_dir($this->logDir)){
                $grep = "awk '{print FILENAME \": \" $0}'";
            }else{
                $grep = "cat";
            }
        }
        // 使用 find 找到所有日志文件.结合 grep 进行内容搜索
        // awk 实现分页.跳过 $this->offset 条记录.显示 $this->limit 条  tac 可按时间排序
        $command = "$findCommand -exec ".$grep." {} + | awk 'NR > $this->offset && NR <= ($this->offset + $this->limit) {print NR \").  \" $0}'";

        return $this->runCommand($command);
    }

    /**
     * 获取总数量
     * @return array
     * @throws HooException
     */
    public function count()
    {
        if(empty($this->keyword)){
            $escapedKeyword = "''";
        }else{
            // 确保关键词和目录路径是安全的.避免命令注入
            $escapedKeyword = escapeshellarg($this->keyword);
        }
        $escapedLogDir = escapeshellarg($this->logDir);

        // 如果指定扩展名.限制只搜索特定类型文件
        $findCommand = "find $escapedLogDir -type f";
        if (!empty($this->fileExtension)) {
            $escapedExtension = escapeshellarg("*.$this->fileExtension");
            $findCommand .= " -name $escapedExtension";
        }
        $grep = "grep -i {$escapedKeyword}";
        if($escapedKeyword == "''"){
            $grep = "cat";
        }
        $command = "$findCommand -exec ".$grep." {} + | wc -l";

        return $this->runCommand($command);
    }

    /**
     * 执行命令
     * @param $command
     * @return array
     * @throws HooException
     */
    private function runCommand($command)
    {
        // 执行命令
        $output = [];
        $returnVar = 0;
        $before_time = microtime(true);
        exec($command, $output, $returnVar);
        $after_time = microtime(true);

//        # 记录日志 格式化记录数组
//        Log::channel('debug')->log('info', "【命令】", [
//            '耗时' => round($after_time - $before_time, 3) * 1000 . 'ms',
//            'exec' => $command,
//        ]);

        if ($returnVar !== 0 && empty($output)) {
            throw new HooException("搜索过程中出现错误或未找到匹配结果！");
        }
        return $output;
    }
}


