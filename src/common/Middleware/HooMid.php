<?php

namespace hoo\io\common\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class HooMid
{
    /**
     * 请求中间件 全局
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        # 获取当前路由
        $reqPath = $request->path();
        # 如果第一位是斜杠则去掉
        if (substr($reqPath, 0, 1) == '/') {
            $reqPath = substr($reqPath, 1);
        }
        # 判断是否包含斜杠 如果不包含则末尾增加斜杠
        if (strpos($reqPath, '/') === false) {
            $reqPath = $reqPath . '/';
        }

        # 不包含
        if (!$this->fnmatchs('clockwork/*',$reqPath) and
            !$this->fnmatchs('__clockwork/*',$reqPath) and
            !$this->fnmatchs('log-viewer/*',$reqPath)
        ) {
            Log::channel('debug')->log('info', "【请求参数展示】", [
                '格式化展示'=>$request->input() ?? [],
                'json展示'=>json_encode($request->input() ?? [], JSON_UNESCAPED_UNICODE),
            ]);
        }

        # clockwork
        if($this->fnmatchs('clockwork/*',$reqPath) || $this->fnmatchs('__clockwork/*',$reqPath)) {
            # 判断是否有权限
            if (!Gate::allows('hooAuth')){
                header('HTTP/1.1 500 Server Error');
                exit();
            }

            if($this->fnmatchs('clockwork/app',$reqPath)){
                echo View::file(__DIR__ . '/../../monitor/clockwork/views/index.blade.php')->render();
                exit();
            }
        }

        # log-viewer
        if($this->fnmatchs('log-viewer/*',$reqPath)) {
            # 判断是否有权限
            if (!Gate::allows('hooAuth')){
                header('HTTP/1.1 500 Server Error');
                exit();
            }
        }

        $Response = $next($request);
        # 添加getData方法 兼容【日志中心修改过的Clockwork】
        if ($Response instanceof Response){
            $Response->macro('getData',function(){
                $object = new \stdClass();
                $object->message = 'ok';
                $object->data = [];
                $object->code = 200;
            });
        }

        return $Response;
    }

    private function fnmatchs($pattern,$filename)
    {
        if(is_string($filename)){
            return fnmatch($pattern,$filename);
        }elseif(is_array($filename)){
            foreach ($filename as $v){
                if(fnmatch($pattern,$v)){
                    return true;
                }
            }
        }
        return false;
    }
}
