<?php

namespace hoo\io\http;


use Cloudladder\Http\Client;
use GuzzleHttp\Exception\GuzzleException;
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
                $res = $response->getBody()->getContents();
            }catch (\Error $e){$res = '';}

            // 记录请求日志
            $this->log(
                $before_time, $after_time,
                $method, $uri, $options,
                $res,$err
            );

        } catch (\Exception $e) {
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
     * @param $options
     * @param $res
     * @param $err
     * @return void
     */
    protected function log($before_time, $after_time, $method, $uri, $options, $res, $err)
    {
        # 如果是json格式则格式化 保留中文和斜杠展示
        $res_json = '';
        if ($this->isJson($res)) {
            $res = json_decode($res, true);
            $res_json = json_encode($res, JSON_UNESCAPED_UNICODE);
        }else{
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
            'url' => $uri,
            'method' => $method,
            'options' => $options,
            'response' => $res,
            'err' => $err,
            '入参出参json展示' => $json_show
        ]);

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
