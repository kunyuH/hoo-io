<?php

namespace hoo\io\monitor\clockwork\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ClockworkMid
{
    /**
     * 对外提供的接口请求日志记录
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->path() == 'clockwork/app') {
            echo View::file(__DIR__ . '/../views/index.blade.php',[
                'cdn' => 'https://js.tuguaishou.com/other/clockwork/',
            ])->render();
            exit();
        }
        return $next($request);
    }
}
