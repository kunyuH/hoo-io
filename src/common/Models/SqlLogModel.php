<?php

namespace hoo\io\common\Models;

use hoo\io\common\Services\ContextService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class SqlLogModel extends BaseModel
{
    protected $table = 'hm_sql_log';

    /**
     * 记录程序访问的第三方接口请求日志
     * @param $run_time
     * @param $database
     * @param $connection_name
     * @param $sql
     * @return void
     */
    public function log($run_time,$database,$connection_name,$sql)
    {
        # 排除自己;api和http日志
        if (!in_string([
            $this->getTableName(),
            (new HttpLogModel())->getTableName(),
            (new ApiLogModel())->getTableName()
        ],$sql)) {

            # true 未开启sql日志记录
            if(!$this->isRecord()){
                return;
            }
            
            # 字符串长度超出 则不记录
            if (strlen($sql) > Config::get('hoo-io.HM_API_HTTP_LOG_LENGTH')) {
                $sql = 'sql is too long';
            }
            # 暂存到上下文中
            ContextService::setSqlLog([
                'app_name'=>$_SERVER['APP_NAME']??'',
                'hoo_traceid'=>ContextService::getHooTraceId(),
                'run_time'=>$run_time,
                'database'=>$database,
                'connection_name'=>$connection_name,
                'sql'=>$sql,
                'run_trace'=>get_run_trace(),
                'run_path'=>get_run_path(),
                'created_at'=>date('Y-m-d H:i:s'),
            ]);
        }
    }

    /**
     * 将暂存区数据批量存储到日志库中
     * @return void
     */
    public function logSave()
    {
        # true 未开启sql日志记录
        if(!$this->isRecord()){
            return;
        }
        # 检验是否存在http日志表
        if (Schema::hasTable($this->getTable())) {
            $sql_log = ContextService::getSqlLog();
            if ($sql_log){
                self::insert($sql_log);
                # 清空暂存区
                ContextService::clearSqlLog();
            }
        }
    }

    /**
     * 是否记录sql日志
     * @return bool
     */
    private function isRecord()
    {
        # true 已开启HHTTP日志记录
        if(Config::get('hoo-io.HM_SQL_LOG')){
            # true 命令行情况下
            if(App::runningInConsole()){
                # true 命令行开启HHTTP日志记录
                if(Config::get('hoo-io.HM_SQL_COMMAND_LOG')){
                    return true;
                }else{
                    return false;
                }
            }else{
                return true;
            }
        }
        return false;
    }
}

