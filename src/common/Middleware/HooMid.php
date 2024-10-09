<?php

namespace hoo\io\common\Middleware;

use Closure;
use hoo\io\common\Support\Facade\HooSession;
use hoo\io\monitor\ArcanedevLogViewer\ArcanedevLogViewerService;
use hoo\io\monitor\clockwork\ClockworkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
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

        # clockwork
        if(ho_fnmatchs('clockwork/*',$reqPath) || ho_fnmatchs('__clockwork/*',$reqPath)) {
            # 判断是否有权限
            if (!Gate::allows('hooAuth')){
                header('HTTP/1.1 500 Server Error');
                exit();
            }

            if(ho_fnmatchs('clockwork/app',$reqPath)){
                echo View::file(__DIR__ . '/../../monitor/clockwork/views/index.blade.php')->render();
                exit();
            }
        }

        # log-viewer
        if(ho_fnmatchs(config('log-viewer.route.attributes.prefix','log-viewer').'/*',$reqPath)) {
            # 从session中提取用户进入的目录 之后设置log-viewer控件展示日志的根目录
            $path = HooSession::get('log-viewer.storage-path');
            if($path){
                Config::set('log-viewer.storage-path', $path);
            }

            # 判断是否有权限
            if (!Gate::allows('hooAuth')){
                header('HTTP/1.1 500 Server Error');
                exit();
            }
        }
        $response = $next($request);
        /**
         * gupo 日志中心 改造的clockwork 展示异常处理 修复异常报错
         */
        $response = (new ClockworkService())->gupoClockErrorCorrect($request,$response);

        /**
         * gupo 日志中心 改造 ArcanedevLogViewer 日志view页面 静态资源链接替换，防止无法访问
         */
        $response = (new ArcanedevLogViewerService())->replaceStaticResourceLink($request,$response,$reqPath);

        return $response;
    }
}
