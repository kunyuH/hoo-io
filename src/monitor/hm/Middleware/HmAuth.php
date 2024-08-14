<?php

namespace hoo\io\monitor\hm\Middleware;

use Closure;
use hoo\io\common\Support\Facade\HooSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Exception;

class HmAuth
{
    /**
     * hm 监控系统权限验证
     * @param Request $request
     * @param Closure $next
     * @return mixed|void
     */
    public function handle(Request $request, Closure $next)
    {
        # 判断是否有权限 true 没有权限
        if (!Gate::allows('hooAuth')){
            # 重定向跳转
            header("Location: ".jump_link("/hm/login/index"));
            exit();
        }
        return $next($request);
    }
}
