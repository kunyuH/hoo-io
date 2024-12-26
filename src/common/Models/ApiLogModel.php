<?php

namespace hoo\io\common\Models;

use hoo\io\common\Services\ContextService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class ApiLogModel extends BaseModel
{
    protected $table = 'hm_api_log';

    /**
     * 保存接口请求日志
     * @param Request $request
     * @param Response $response
     * @param $input
     * @param $output
     * @param $run_time
     * @return void
     */
    public function log(Request $request, Response $response, $input, $output, $run_time)
    {
        try{
            # 检验是否存在http日志表
            if (Config::get('hoo-io.HM_API_LOG') && Schema::hasTable($this->getTable())) {
                # 字符串长度超出 则不记录
                if (strlen($input) > Config::get('hoo-io.HM_API_HTTP_LOG_LENGTH')) {
                    $input = 'input is too long';
                }
                # 字符串长度超出 则不记录
                if (strlen($output) > Config::get('hoo-io.HM_API_HTTP_LOG_LENGTH')) {
                    $output = 'output is too long';
                }
                self::insert([
                    'app_name'=>$_SERVER['APP_NAME']??'',
                    'hoo_traceid'=>ContextService::getHooTraceId(),
                    'user_id'=>$request->input(Config::get('hoo-io.HM_API_LOG_USER_FILED'),''),
                    'domain'=>$request->getHost().':'.$request->getPort(),
                    'path'=>$request->path(),
                    'method'=>$request->method(),
                    'run_time'=>round($run_time, 3) * 1000,
                    'user_agent'=>$request->server('HTTP_USER_AGENT'),
                    'input'=>$input,
                    'output'=>$output,
                    'status_code'=>$response->getStatusCode(),
                    'ip'=>$request->ip(),
                    'created_at'=>date('Y-m-d H:i:s'),
                ]);
            }
        }catch (\Throwable $e){}
    }

    /**
     * 关联依赖HTTP服务日志表
     * 一对多
     */
    public function HttpLog()
    {
        return $this->hasMany(HttpLogModel::class, 'hoo_traceid','hoo_traceid');
    }

    /**
     * 关联依赖数据服务日志表
     * 一对多
     */
    public function SqlLog()
    {
        return $this->hasMany(SqlLogModel::class, 'hoo_traceid','hoo_traceid');
    }
}
