<?php

namespace hoo\io\common\Middleware;

use Closure;
use hoo\io\common\Models\ApiLogModel;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ApiLogMid
{
    /**
     * 请求中间件 记录接口日志入参 出参日志
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $t1 = microtime(true);
        $input = $request->input();
        if ($this->via($request->path())) {
            Log::channel('debug')->log('info', "【接口入参】", [
                '格式化展示'=>$input ?? [],
                'json展示(流转请复制这块数据)'=>json_encode($input ?? [], JSON_UNESCAPED_UNICODE),
            ]);
        }

        $response = $next($request);
        $output = $this->getOutput($response);
        $t2 = microtime(true);

        if ($this->via($request->path())) {
            Log::channel('debug')->log('info', "【接口出参】", [
                '格式化展示'=>$output,
            ]);
        }
        if ($this->via($request->path())) {
            (new ApiLogModel())->log($request, $response,
                json_encode($input,JSON_UNESCAPED_UNICODE),
                json_encode($output,JSON_UNESCAPED_UNICODE),
                $t2-$t1);
        }

        return $response;
    }

    private function getOutput(Response $response)
    {
        $output = $response->getContent();
        if($output == ''){
            $output = [];
        }elseif(is_json($output)){
            $output = json_decode($output,JSON_UNESCAPED_UNICODE);
        }else{
            # 截取50个字符
            $output = ['非json格式，截取部分字符：'.mb_substr($output, 0, 50, 'utf-8')];
        }
        return $output;
    }

    /**
     * 排除的路由
     */
    private function via($reqPath)
    {
        # 如果第一位是斜杠则去掉
        if (substr($reqPath, 0, 1) == '/') {
            $reqPath = substr($reqPath, 1);
        }
        # 判断是否包含斜杠 如果不包含则末尾增加斜杠
        if (strpos($reqPath, '/') === false) {
            $reqPath = $reqPath . '/';
        }
        # 不包含
        if (!ho_fnmatchs('clockwork/*',$reqPath) and
            !ho_fnmatchs('__clockwork/*',$reqPath) and
            !ho_fnmatchs('log-viewer/*',$reqPath) and
            !ho_fnmatchs('hm-r/*',$reqPath) and
            !ho_fnmatchs(config('log-viewer.route.attributes.prefix','log-viewer').'/*',$reqPath)
        ) {
            return true;
        }else{
            return false;
        }
    }
}
