<?php

namespace hoo\io\http;


use Cloudladder\Http\Client;
use GuzzleHttp\Exception\GuzzleException;
use hoo\io\common\Models\HttpLogModel;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Psr\Http\Message\ResponseInterface;

/**
 * http服务
 */
class HHttp extends Client
{
    /**
     * @param $method
     * @param $uri
     * @param array $options
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function request($method, $uri = '', array $options = []): ResponseInterface
    {
        $before_time = microtime(true);

        # 调用父类request方法 捕获异常 且记录 错误内容
        list($response,$err) = $this->parentRequest($method, $uri, $options);

        $after_time = microtime(true);
        try {

            try{
                $resStr = $response->getBody()->getContents();
            }catch (\Error $e){$resStr = '';}

            // 记录请求日志
            $this->log(
                $before_time, $after_time,
                $method, $uri, $options,
                $resStr,$err
            );

        } catch (\Throwable $e) {}

        // 重置响应主体流
        $response->getBody()->rewind();
        return $response;
    }

    /**
     * @param $method
     * @param $uri
     * @param $options
     * @return array
     * @throws GuzzleException
     */
    public function parentRequest($method, $uri, $options)
    {
        $err = '';
        try{
            $response = parent::request($method, $uri, $options);
        }catch (\Exception $e){
            $err = $e->getMessage();
            try{
                $response = $e->getResponse();
            }catch (\Throwable $e){
                $response = null;
            }
        }
        return [$response,$err];
    }

    /**
     * 记录日志
     * @param $method
     * @param $uri
     * @param $options //入参 array
     * @param $resStr  //返回的原始数据
     * @param $err
     * @return void
     */
    protected function log($before_time, $after_time, $method, $uri, $options, $resStr, $err)
    {
        # 如果是json格式则格式化 保留中文和斜杠展示
        $res_json = '';
        if (is_json($resStr)) {
            $res = json_decode($resStr, true);
            $res_json = json_encode($res, JSON_UNESCAPED_UNICODE);
        }else{
            $res_json = $resStr;
        }


        $json_show['url'] = $uri;
        $json_show['method'] = $method;
        if(isset($options['headers'])) {
            $json_show['headers'] = json_encode($options['headers'], JSON_UNESCAPED_UNICODE);
        }
        if(isset($options['query'])) {
            $json_show['query'] = json_encode($options['query'], JSON_UNESCAPED_UNICODE);
        }
        if(isset($options['data'])) {
            $json_show['data'] = json_encode($options['data'], JSON_UNESCAPED_UNICODE);
        }
        if(isset($options['json'])) {
            $json_show['json'] = json_encode($options['json'], JSON_UNESCAPED_UNICODE);
        }
        $json_show['response'] = $res_json;

        $runTrace = $this->getRunTrace();
        $runPath = $this->getRunPath();

        # 记录日志 格式化记录数组
        Log::channel('debug')->log('info', "【H-HTTP】", [
            '耗时' => round($after_time - $before_time, 3) * 1000 . 'ms',
            'run_trace' => $runTrace,
            'url' => $uri,
            'method' => $method,
            'options' => $options,
            'response' => $resStr,
            'err' => $err,
            '入参出参json展示' => $json_show
        ]);

        HttpLogModel::log(round($after_time - $before_time, 3) * 1000,
            parse_url($uri)['path']??'',
            $uri,
            $method,
            json_encode($options,JSON_UNESCAPED_UNICODE),
            $resStr,
            $err,
            $runTrace,
            $runPath
        );
    }

    /**
     * 判断返回字符串是否是json格式
     * @param $res
     * @return bool
     */
    protected function isJson($res): bool
    {
        // 判断返回字符串是否是json格式
        if (is_null(json_decode($res, true))) {
            return false;
        }
        return true;
    }

    /**
     * 获取发起http请求的代码位置
     * @return string
     */
    private function getRunTrace()
    {
        # 调用的代码位置 并且只要相对位置
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 4)[3];
        # 获取当前应用所在根目录
        $file = str_replace(base_path(), '', $trace['file']);
        $traceStr = $file.':'.$trace['line'];

        return $traceStr;
    }

    /**
     * 获取发起http请求的路径 或 命令
     * @return void
     */
    private function getRunPath()
    {
        $runPath = '';
        if (App::runningInConsole()){
            $runPath = implode(' ', $_SERVER['argv']??[]);
        }else{
            $runPath = request()->path();
        }
       return $runPath;
    }
}
