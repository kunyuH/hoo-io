<?php

namespace hoo\io\monitor\hm\Models;

use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use Throwable;
use Exception;

class LogicalPipelinesArrangeModel extends BaseModel
{
    protected $table = 'hm_logical_pipelines_arrange';

    /**
     * 运行逻辑线
     * @param $id
     * @return void
     */
    public static function run($id)
    {
        $pipeline = self::query()
            ->where('logical_pipeline_id',$id)
            ->get()->toArray();

        $pipeline = LogicalPipelinesArrangeModel::arrange($pipeline);

        return LogicalPipelinesArrangeModel::exec($pipeline);
    }

    /**
     * 将编排结果有序排列
     * @param $pipeline
     * @return array
     */
    public static function arrange($pipeline)
    {
        # next_id 数据预处理 为null 和 为'' 都置为0
        foreach ($pipeline as $k=>$v){
            if($v['next_id'] === null){
                $pipeline[$k]['next_id'] = 0;
            }else if($v['next_id'] === ''){
                $pipeline[$k]['next_id'] = 0;
            }
        }

        # 先找到最后一个
        foreach ($pipeline as $k=>$v){
            if(empty($v['next_id'])){
                $last = $v;
                break;
            }
        }
        # 将数据 按照next_id 作为key，存入数组
        $data = [];
        foreach ($pipeline as $k=>$v){
            $data[$v['next_id']] = $v;
        }
        # 递归处理
        $out = self::recursion($data,$last);
        # 倒排
        $out = array_reverse($out);
        return $out;
    }

    /**
     * 递归从最后一项逐步找到全部
     * @param $data
     * @param $last
     * @param $out
     * @return array|mixed
     */
    private static function recursion($data,$last,$out=[],$t=0)
    {
        $t++;
        if($t>1000){
            # 防止死循环
            return $out;
        }
        $out[] = $last;
        if(isset($data[$last['id']])){
            $out = self::recursion($data,$data[$last['id']],$out,$t);
        }
        return $out;
    }

    /**
     * 逻辑线运行核心
     * @param $pipeline
     * @return array
     */
    private static function exec($pipeline)
    {
        $resData = [];
        foreach ($pipeline as $k=>$v){
            $block = LogicalBlockModel::find($v['logical_block_id']);
            list($resData,$error) = self::logicalBlockExec($block->logical_block,$block->name,$resData);
            if(!empty($error)) {
//                dd($error);
                throw new Exception($error->getMessage(), $error->getCode());
//                if (config('app.debug', false)) {
//                    echo $error->getMessage() . PHP_EOL;
//                    echo PHP_EOL;
//                    echo $error->getTraceAsString();
//                    return [];
//                } else {
//                    throw new Exception($error->getMessage(), $error->getCode());
//                }
            }
        }
        return $resData;
    }

    /**
     * 逻辑单元运行
     * @param $logical_block
     * @param $name
     * @param $resData
     * @return array|mixed
     */
    public static function logicalBlockExec($logical_block,$name='',$resData=[])
    {
        $before_time = microtime(true);
        $inputData = $resData;
        $error = null;
        try{
            # 加载时应用的类名
            $class_name = 'Foo_'.md5(time().Uuid::uuid1()->toString());
            # 字符串替换
            $logical_block = str_replace('Foo',$class_name,$logical_block);

            // 将变量内容写入临时文件
            $tmpfname = tempnam(sys_get_temp_dir(), 'logical-block:');
            file_put_contents($tmpfname, $logical_block);

            include $tmpfname;
            unlink($tmpfname);

            $class = new \ReflectionClass($class_name);
            $instance = $class->newInstanceArgs();

            if(!empty($resData)){
                $resData = $instance->run(...$resData);
            }else{
                $resData = $instance->run();
            }

        }catch (Throwable $e){
            if(file_exists($tmpfname)){
                unlink($tmpfname);
            }
            $error = $e;
        }
        $after_time = microtime(true);

        self::log($name,$before_time,$after_time,$inputData,$resData,$error);

        return [$resData,$error];
    }

    /**
     * 记录日志
     * @param $name
     * @param $before_time
     * @param $after_time
     * @param $inputData
     * @param $resData
     * @param null|Throwable $error
     * @return void
     */
    public static function log($name,$before_time,$after_time,$inputData,$resData,null|Throwable $error)
    {
        # 记录日志 格式化记录数组
        if(empty($error)){
            Log::channel('debug')->log('info', "【logical block】{$name}", [
                '耗时' => round($after_time - $before_time, 3) * 1000 . 'ms',
                'input' => $inputData,
                'out' => $resData,
            ]);
        }else{
            Log::channel('debug')->log('error', "【logical block】{$name}", [
                '耗时' => round($after_time - $before_time, 3) * 1000 . 'ms',
                'input' => $inputData,
                'out' => $resData,
                'error' => [
                    'code' => $error->getCode(),
                    'message' => $error->getMessage(),
                    'file' => $error->getFile(),
                    'line' => $error->getLine(),
//                    'trace' => $error->getTrace()
                ]
            ]);
        }
    }
}
