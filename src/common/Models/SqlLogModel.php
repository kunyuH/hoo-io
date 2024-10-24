<?php

namespace hoo\io\common\Models;

use hoo\io\common\Services\ContextService;
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
            # 检验是否存在http日志表
            if (Schema::hasTable($this->getTableName()) && Config::get('hoo-io.HM_SQL_LOG')) {
                # 字符串长度超出 则不记录
                if (strlen($sql) > Config::get('hoo-io.HM_API_HTTP_LOG_LENGTH')) {
                    $sql = 'sql is too long';
                }
                self::insert([
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
    }
}

