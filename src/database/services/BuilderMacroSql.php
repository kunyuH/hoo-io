<?php

namespace hoo\io\database\services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

/**
 * 宏扩展
 * 用于治理数据查询
 * 分页
 * Class BuilderMacro
 */
class BuilderMacroSql
{
    /**
     * @var Builder $query  查询对象
     */
    public static $query;

    /**
     * @var string $sql  sql语句
     */
    public $sql;

    /**
     * @var array $bindings  绑定参数
     */
    public $bindings;

    /**
     * 设置查询对象
     * @param $query
     * @return $this
     */
    public function getSqlQuery($query): BuilderMacroSql
    {
        static::$query = $query;
        return $this;
    }

    /**
     * 绑定sql参数
     * @param string $sql
     * @param array $bindings
     * @return $this
     */
    public function bindings(string $sql, array $bindings = []): BuilderMacroSql
    {
        $this->sql = $sql;
        list($this->sql,$this->bindings) = BuilderMacroSqlTools::getInBindingsPreprocessing($sql, $bindings);

        return $this;
    }

    /**
     * 获取全部数据
     * @return array
     */
    public function get(): array
    {
        $this->log();
        $t1 = microtime(true);
        # 分页查询
        $data = json_decode(json_encode(static::$query->connection->select($this->sql, $this->bindings)), true);
        $t2 = microtime(true);
        $this->log($t2-$t1);
        return $data;
    }

    /**
     * 记录日志
     * @return void
     */
    protected function log($runtime = 0)
    {
        if($runtime){
            Log::channel('debug')->info("耗时：".$runtime."s");
        }else{
            # 记录日志
            $sql = $this->sql;
            foreach ($this->bindings as $key => $value) {
                $sql = str_replace(':'.$key, "'".$value."'", $sql);
            }
            Log::channel('debug')->info($sql);
        }
    }

    /**
     * 自定义分页
     * @param int $page
     * @param int $per_page
     * @return array
     */
    public function paginate(int $page = 0, int $per_page = 0): array
    {
        # 总条数
        $total = $this->getCountForPagination();
        # 第几页
        $page = $page ?: (int) request()->input('page', 1);
        # 每页显示条数
        $per_page =  $per_page ?: (int) request()->input('per_page', 15);

        # 分页sql片段【重新绑定】
        $sql = $this->sql . " limit ".($page-1)*$per_page.",".$per_page;
        $this->bindings($sql,$this->bindings);

        return [
            'paginate'=>[
                "total" => $total,
                "current_page"=> $page,
                "page_size"=> $per_page
            ],
            'list'=>$this->get(),
        ];
    }

    /**
     * 获取总条数
     * @return int
     */
    public function getCountForPagination(): int
    {
        $this->log();
        $t1 = microtime(true);
        $data = self::$query->connection->affectingStatement($this->sql,$this->bindings);
        $t2 = microtime(true);
        $this->log($t2-$t1);
        return $data;
//        return self::$query->connection->select("select count(1) as count from ({$this->sql}) as count_table",$this->bindings)[0]->count;
    }
}
