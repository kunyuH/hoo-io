<?php

namespace hoo\io\gateway;

use Closure;
use hoo\io\gateway\HttpService;
use hoo\io\monitor\hm\Models\LogicalBlockModel;
use hoo\io\monitor\hm\Support\Facades\LogicalBlock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Pipeline\Pipeline;

/**
 * 服务代理 中间件
 *  用于授权等情况
 *
 * 服务代理 数据模型
 *  使用中间件技术承载逻辑方案
 *  用于处理代理服务 入参出参数据处理
 */
class GatewayMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $middlewares=[];

        /********************中间件可通过传参配置*****************************/
        # 1. 装载接口参数中指定执行的中间件
        list($_, $gateway_mid, $_, $_, $_, $gateway_data_model, $_) = (new HttpService())->getGatewayInfo($request);
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

        # 3. 装载数据处理模型（中间件） 来源于逻辑块
        $gateway_data_model = explode(',', $gateway_data_model);
        if($gateway_data_model[0]??null){
            if(hoo_schema()->hasTable((new LogicalBlockModel())->getTable())){
                foreach ($gateway_data_model as $model){
                    try{
                        // 运行逻辑块
                        $logicalBlock = LogicalBlock::getObject($model);
                        if(!empty($logicalBlock)){
                            $middlewares[] = $logicalBlock;
                        }
                    }catch (\Throwable $e) {}
                }
            }
        }

        // 4.使用管道按顺序执行中间件
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
