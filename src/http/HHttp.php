<?php

namespace hoo\io\http;


use Cloudladder\Http\Client;
use GuzzleHttp\Exception\GuzzleException;
use hoo\io\common\Exceptions\HooException;
use hoo\io\common\Models\HttpLogModel;
use Illuminate\Support\Facades\Log;
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
     * @throws GuzzleException|HooException
     */
    public function request($method, $uri = '', array $options = []): ResponseInterface
    {
        $before_time = microtime(true);

        # 调用父类request方法 捕获异常 且记录 错误内容
        list($response,$err) = $this->parentRequest($method, $uri, $options);

        $after_time = microtime(true);
        try {

            try{
                $res = $response->getBody()->getContents();
            }catch (\Error $e){$res = '';}

            // 记录请求日志
            $this->log(
                $before_time, $after_time,
                $method, $uri, $options,
                $res,$err
            );

        } catch (\Throwable $e) {
            throw new HooException($e->getMessage());
        }

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
     * @param $res  //返回的原始数据
     * @param $err
     * @return void
     */
    protected function log($before_time, $after_time, $method, $uri, $options, $res, $err)
    {
        /**
         * res 是原始接口返回 可能是json字符串 也可能是普通字符串
         *
         * res_arr
         * res为json则 将res转换成数组并赋值给res_arr
         * res非json则 直接将res值赋值给res_arr
         * 使用场景【log记录 格式化展示】
         *
         * res_json
         * res为json则 res_json=res 且将中文转义过
         * res非json则 res_json=res
         * 使用场景【用于log文本记录 用于复制字段的展示；用于记录到数据库】
         *
         */
        $res_arr = null;
        $res_json = '';
        if (is_json($res)){
            $res_arr = json_decode($res, true);
            $res_json = json_encode($res_arr, JSON_UNESCAPED_UNICODE);
        }else{
            $res_arr = $res;
            $res_json = $res;
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


        # 记录日志 格式化记录数组
        Log::channel('debug')->log('info', "【H-HTTP】", [
            '耗时' => round($after_time - $before_time, 3) * 1000 . 'ms',
            'run_trace' => get_run_trace(),
            'url' => $uri,
            'method' => $method,
            'options' => $options,
            'response' => $res_arr,
            'err' => $err,
            '入参出参json展示' => $json_show
        ]);

        (new HttpLogModel())->log(round($after_time - $before_time, 3) * 1000,
            parse_url($uri)['path']??'',
            $uri,
            $method,
            json_encode($options,JSON_UNESCAPED_UNICODE),
            $res_json,
            $err
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
}
