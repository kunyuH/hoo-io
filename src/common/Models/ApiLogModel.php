<?php

namespace hoo\io\common\Models;

use hoo\io\common\Services\ContextService;
use Illuminate\Http\Request;
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
    public static function log(Request $request, Response $response, $input, $output, $run_time)
    {
        try{
            # 检验是否存在http日志表
            if (Schema::hasTable('hm_api_log') && env('HM_API_LOG',true)) {
                self::insert([
                    'app_name'=>$_SERVER['APP_NAME']??'',
                    'hoo_traceid'=>ContextService::getHooTraceId(),
                    'user_id'=>$request->input(env('HM_API_LOG_USER_FILED','member_id'),''),
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
     * 关联依赖服务日志表
     * 一对多
     */
    public function HttpLog()
    {
        return $this->hasMany(HttpLogModel::class, 'hoo_traceid','hoo_traceid');
    }
}
