<?php

namespace hoo\io\monitor\clockwork;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ClockworkService
{
    /**
     * gupo 日志中心 改造的clockwork 展示异常处理
     * @param $Response
     * @return Response|mixed
     */
    public function gupoClockErrorCorrect(Request $request, $response)
    {
        # 添加getData方法 兼容【gupo 日志中心修改过的Clockwork】
        if ($response instanceof Response){
            $response->macro('getData',function(){
                $object = new \stdClass();
                $object->message = 'ok';
                $object->data = [];
                $object->code = 200;
            });
        }

        /**
         * 出现错误 Error loading request metadata
         * 路由 __clockwork
         * only=clientMetrics%2CwebVitals
         * 返回值为[]
         */
        if(ho_fnmatchs('__clockwork/*',$request->path())
            and $request->get('only') == 'clientMetrics,webVitals'
            and empty($response->getData())){
            $response = response()->json([
                'clientMetrics' => [],
                'webVitals' => []
            ]);
        }

        return $response;
    }
}
