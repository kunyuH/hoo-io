<?php

namespace hoo\io\common\Models;

use Illuminate\Support\Facades\Schema;
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

    /**
     * 记录程序访问的第三方接口请求日志
     * @param $run_time
     * @param $path
     * @param $uri
     * @param $method
     * @param $options
     * @param $resStr
     * @param $err
     * @param $runTrace
     * @param $runPath
     * @return void
     */
    public static function log($run_time,$path,$uri,$method,$options,$resStr,$err,$runTrace,$runPath)
    {
        # 检验是否存在http日志表
        if (Schema::hasTable('hm_http_log') && env('HM_HTTP_LOG',true)) {
            if(empty(self::$requestUuid)){
                self::$requestUuid = Str::random(5);
            }
            # 字符串长度超出 则不记录
            if (strlen($resStr) > 5000) {
                $resStr = 'response is too long';
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
}
