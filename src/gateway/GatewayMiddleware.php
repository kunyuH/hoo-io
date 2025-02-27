<?php

namespace hoo\io\gateway;

use Closure;
use hoo\io\common\Services\ContextService;
use hoo\io\gateway\HttpService;
use hoo\io\monitor\hm\Models\LogicalBlockModel;
use hoo\io\monitor\hm\Support\Facades\LogicalBlock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        $mids = Route::getMiddleware();
        /********************中间件可通过传参配置*****************************/
        # 1. 装载配置中最先执行的中间件
        $first_mid = config('hoo-io.GATE_FIRST_MID');
        $middlewares = array_merge($middlewares, $this->getMidObj($first_mid));
        
        # 2. 装载接口参数中指定执行的中间件
        list($_, $gateway_mid, $_, $_, $_, $gateway_data_model, $_) = (new HttpService())->getGatewayInfo($request);
        $gateway_mid = explode(',', $gateway_mid);
        if($gateway_mid[0]??null){
            $middlewares = array_merge($middlewares, $this->getMidObj($gateway_mid));
        }else{
            /********************如果没有传递 则默认执行的中间件*****************************/
            $default_mids = config('hoo-io.GATE_DEFAULT_MID');
            $middlewares = array_merge($middlewares, $this->getMidObj($default_mids));
        }

        # 3. 装载数据处理模型（中间件） 来源于逻辑块
        $gateway_data_model = explode(',', $gateway_data_model);
        $middlewares = array_merge($middlewares, $this->getMidObj($gateway_data_model));

        # 4. 装载配置中最后执行的中间件
        $last_mid = config('hoo-io.GATE_LAST_MID');
        $middlewares = array_merge($middlewares, $this->getMidObj($last_mid));

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

    /**
     * 获取中间件对象
     * @param $mids
     * @return array
     */
    private function getMidObj($mids)
    {
        # 判断mid是否数组
        if(!is_array($mids)){
            $mids = [$mids];
        }
        $middlewares = [];

        foreach ($mids as $mid){
            $mid = trim($mid);
            if(empty($mid)){
                continue;
            }
            # 1.先在程序设定好的中间件中寻找
            $route_mids = Route::getMiddleware();
            if(isset($route_mids[$mid])){
                $middlewares[] = (new $route_mids[$mid]());
            }

            # 2.在逻辑块中寻找
            if(ContextService::isLogicalBlockInstall()){
                try{
                    // 运行逻辑块
                    $logicalBlock = LogicalBlock::getObject($mid);
                    if(!empty($logicalBlock)){
                        $middlewares[] = $logicalBlock;
                    }
                }catch (\Throwable $e) {
                    Log::channel('debug')->log('info', "代理服务-获取中间件对象异常", [
                        'error'=>$e->getMessage(),
                    ]);
                }
            }
        }
        return $middlewares;
    }
}
