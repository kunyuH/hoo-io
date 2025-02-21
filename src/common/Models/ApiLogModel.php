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
            $path = $request->path();

            # 首位补充斜杠
            if (strpos($path, '/') !== 0) {
                $path = '/'.$path;
            }

            # true 不否记录api日志
            if(!$this->isRecord($path)){
                return;
            }

            # 检验是否存在http日志表
            if (hoo_schema()->hasTable($this->getTable())) {
                # 字符串长度超出 则只截取长度以内的字符
                $HM_API_HTTP_LOG_LENGTH = Config::get('hoo-io.HM_API_HTTP_LOG_LENGTH');

                if (strlen($input) > $HM_API_HTTP_LOG_LENGTH) {
                    $input = "长度超出，截取部分==>".mb_substr($input, 0, $HM_API_HTTP_LOG_LENGTH, "UTF-8")."...";
                }
                # 字符串长度超出 则只截取长度以内的字符
                if (strlen($output) > $HM_API_HTTP_LOG_LENGTH) {
                    $output = "长度超出，截取部分==>".mb_substr($output, 0, $HM_API_HTTP_LOG_LENGTH, "UTF-8")."...";
                }
                self::insert([
                    'app_name'=>$_SERVER['APP_NAME']??'',
                    'hoo_traceid'=>ContextService::getHooTraceId(),
                    'user_id'=>$request->input(Config::get('hoo-io.HM_API_LOG_USER_FILED'),''),
                    'domain'=>$request->getHost().':'.$request->getPort(),
                    'path'=>$path,
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
     * 是否记录api日志
     * @return bool
     */
    private function isRecord($path): bool
    {

        # true 不记录api 日志
        if(!Config::get('hoo-io.HM_API_LOG')){
            return false;
        }
        $not_routes = Config::get('hoo-io.HM_API_LOG_NOT_ROUTE');
        $not_routes = explode(',',$not_routes);

        # 允许不记录日志的路由  匹配 存在则直接返回false
        foreach ($not_routes as $route){
            if(ho_fnmatchs($route,$path)){
                return false;
            }
        }
        return true;
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
