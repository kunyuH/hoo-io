<?php

namespace hoo\io\common\Models;

use hoo\io\common\Services\ContextService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class HttpLogModel extends BaseModel
{
    protected $table = 'hm_http_log';

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
        if (Schema::hasTable('hm_http_log') && Config::get('hoo-io.HM_HTTP_LOG')) {
            # 字符串长度超出 则不记录
            if (strlen($options) > Config::get('hoo-io.HM_API_HTTP_LOG_LENGTH')) {
                $options = 'options is too long';
            }
            # 字符串长度超出 则不记录
            if (strlen($resStr) > Config::get('hoo-io.HM_API_HTTP_LOG_LENGTH')) {
                $resStr = 'response is too long';
            }
            self::insert([
                'app_name'=>$_SERVER['APP_NAME']??'',
                'hoo_traceid'=>ContextService::getHooTraceId(),
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

