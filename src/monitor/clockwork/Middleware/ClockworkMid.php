<?php

namespace hoo\io\monitor\clockwork\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Exception;

class ClockworkMid
{
    /**
     * 请求中间件
     * 设置clockwork监控插件可视化资源cdn
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->path() == 'clockwork/app') {

            # 判断是否有权限
            if (!Gate::allows('viewClockwork')){
                header('HTTP/1.1 500 Server Error');
                exit();
            }
            echo View::file(__DIR__ . '/../views/index.blade.php',[
                'cdn' => 'https://js.tuguaishou.com/other/clockwork/',
            ])->render();
            exit();
        }
        return $next($request);
    }
}
