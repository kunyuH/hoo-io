<?php

namespace hoo\io\common\Services;

use Illuminate\Support\Str;

/**
 * 用于存储上下文信息
 */
class ContextService extends BaseService
{
    /**
     * 服务运行流水号 用于运行链路追踪
     * 比如 http日志
     * hoo_traceid
     */
    public static $hoo_traceid = null;
    public static function getHooTraceId()
    {
        if(empty(self::$hoo_traceid)){
            self::$hoo_traceid = ho_uuid();
        }
        return self::$hoo_traceid;
    }

    /**
     * 用于存储运行中执行的sql信息
     * @var array
     */
    public static $sql_log = [];
    public static function setSqlLog($sqlLog){
        # 暂存区保护 防止内存溢出
        # 超出1000个记录后不再写入
        if(count(self::$sql_log) < 1000){
            self::$sql_log[] = $sqlLog;
        }
    }
    public static function getSqlLog(){
        return self::$sql_log;
    }
    public static function clearSqlLog(){
        self::$sql_log = [];
    }
}
