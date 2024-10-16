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
}
