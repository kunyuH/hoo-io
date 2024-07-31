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

        $response = parent::request($method, $uri, $options);

        $after_time = microtime(true);
        try {
            // 记录请求日志
            $this->log(
                $before_time, $after_time,
                $method, $uri, $options,
                $response->getBody()->getContents()
            );
        } catch (\Exception $e) {
        }
        // 重置响应主体流
        $response->getBody()->rewind();
        return $response;
    }

    /**
     * 记录日志
     * @param $method
     * @param $uri
     * @param $options
     * @param $res
     * @return void
     */
    protected function log($before_time, $after_time, $method, $uri, $options, $res)
    {
        # 如果是json格式则格式化 保留中文和斜杠展示
        if ($this->isJson($res)) {
            $res = json_decode($res, true);
            $res = json_encode($res, JSON_UNESCAPED_UNICODE);
        }
        # 记录日志 格式化记录数组
        Log::channel('debug')->log('info', "【H-HTTP】", [
            '耗时' => round($after_time - $before_time, 3)*1000 . 'ms',
            'url' => $uri,
            'method' => $method,
            'options' => $options,
            'response' => $res
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
