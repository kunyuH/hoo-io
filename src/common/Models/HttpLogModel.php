<?php

namespace hoo\io\common\Models;

use hoo\io\common\Services\ContextService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\App;

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
     * @return void
     */
    public function log($run_time,$path,$uri,$method,$options,$resStr,$err)
    {
        # true 不否记录http日志
        if(!$this->isRecord()){
            return;
        }

        # 检验是否存在http日志表
        if (hoo_schema()->hasTable($this->getTable())) {
            # 字符串长度超出 则只截取长度以内的字符
            $HM_API_HTTP_LOG_LENGTH = Config::get('hoo-io.HM_API_HTTP_LOG_LENGTH');

            if (strlen($options) > $HM_API_HTTP_LOG_LENGTH) {
                $options = "长度超出，截取部分==>".mb_substr($options, 0, $HM_API_HTTP_LOG_LENGTH, "UTF-8")."...";
            }
            # 字符串长度超出 则只截取长度以内的字符
            if (strlen($resStr) > $HM_API_HTTP_LOG_LENGTH) {
                $resStr = "长度超出，截取部分==>".mb_substr($resStr, 0, $HM_API_HTTP_LOG_LENGTH, "UTF-8")."...";
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
                'run_trace'=>get_run_trace(),
                'run_path'=>get_run_path(),
                'created_at'=>date('Y-m-d H:i:s'),
            ]);
        }
    }

    /**
     * 是否记录http日志
     * @return bool
     */
    private function isRecord()
    {
        # true 已开启HHTTP日志记录
        if(Config::get('hoo-io.HM_HTTP_LOG')){
            # true 命令行情况下
            if(App::runningInConsole()){
                # true 命令行开启HHTTP日志记录
                if(Config::get('hoo-io.HM_COMMAND_HTTP_LOG')){
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

