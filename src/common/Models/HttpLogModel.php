<?php

namespace hoo\io\common\Models;

use Illuminate\Support\Str;

class HttpLogModel extends BaseModel
{
    protected $table = 'hm_http_log';

    /**
     * 针对此次请求的UUID
     *
     * @var bool
     */
    protected static  $requestUuid = '';

    public static function log($run_time,$path,$uri,$method,$options,$resStr,$err,$runTrace,$runPath)
    {
        if(empty(self::$requestUuid)){
            self::$requestUuid = Str::random(5);
        }
        self::insert([
            'app_name'=>$_SERVER['APP_NAME']??'',
            'uuid'=>self::$requestUuid,
            'run_time'=>$run_time,
            'path'=>$path,
            'url'=>$uri,
            'method'=>$method,
            'options'=>$options,
            'response'=>$resStr,
            'err'=>$err,
            'run_trace'=>$runTrace,
            'run_path'=>$runPath,
            'created_at'=>date('Y-m-d H:i:s'),
        ]);
    }
}
