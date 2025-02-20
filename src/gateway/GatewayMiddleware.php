<?php

namespace hoo\io\gateway;

use Closure;
use hoo\io\gateway\HttpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Pipeline\Pipeline;

/**
 * 服务代理中间件
 */
class GatewayMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $middlewares=[];

        /********************中间件可通过传参配置*****************************/
        # 1. 装载接口参数中指定执行的中间件
        list($_, $gateway_mid, $_, $_, $_, $_) = (new HttpService())->getGatewayInfo($request);
        $gateway_mid = explode(',', $gateway_mid);
        if($gateway_mid[0]??null){
            $mids = Route::getMiddleware();
            $middlewares = [];
            foreach ($gateway_mid as $mid){
                if(isset($mids[$mid])){
                    $middlewares[] = (new $mids[$mid]());
                }
            }
        }

        # 2. 装载配置中最后执行的中间件
        $last_mid = config('hoo-io.GATE_LAST_MID');
        if(!empty($last_mid)){
            $middlewares[] = (new $last_mid());
        }

        // 3.使用管道按顺序执行中间件
        if(!empty($middlewares)){
            return app(Pipeline::class)
                ->send($request)
                ->through($middlewares)
                ->then($next);
        }else{
            return $next($request);
        }
    }
}
