<?php

namespace hoo\io\http;

use Exception;
use Cloudladder\Http\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

/**
 * Class HttpService
 * 通用http服务
 */
abstract class AHttpService
{
    # api前缀
    protected $api_prefix = '';

//    # 服务code
//    protected $service_code = '';
    protected $service = '';

    /**
     * @var mixed   服务地址 完整路径
     */
    protected $url;
    /**
     * @var mixed   请求方式
     */
    protected $method;
    /**
     * @var mixed   请求头
     */
    protected $headers = [];
    /**
     * @var mixed   请求数据
     */
    protected $params;

    /**
     * @var mixed   body请求数据
     */
    protected $data;

    /**
     * @var mixed   返回数据
     */
    protected $res_data;

    /**
     * 初始化
     * @param $service_code
     */
    public function __construct($service=null)
    {
//        $this->service_code = $service_code;
        $this->service = $service;
    }

    /**
     * 设置请求信息
     * 如果传递的接口中已经存在参数 则拿出来放到params中 且优先级高于传入的params
     * @param null $api
     * @param string $method
     * @param null $params
     * @param null $data
     * @return $this
     */
    public function setReq($api=null, $method="GET", $params=null, $data=null): AHttpService
    {
        # 1.兼容api路径  第一个字符串不是/则加上/
        if (!str_starts_with($api, '/')) {$api = '/' . $api;}

        # 2.兼容api路径 如果已经有前缀则去除前缀
        if (str_starts_with($api, $this->api_prefix)) {
            $api = str_replace($this->api_prefix, '', $api);
        }

        # 3.拼接完整url
        $host = $this->service;
        # 最后一位是/则去掉
        if (str_ends_with($host, '/')) {$host = substr($host, 0, -1);}
        $url = $host . $this->api_prefix . $api;


        # 4.如果路由中已经存在参数 则拿出来放到params中 且优先级高于传入的params
        $parts = parse_url($url);
        if(isset($parts['query'])){
            parse_str($parts['query'], $query);
            $params = array_merge((array)$params, $query);
            $url = $parts['scheme'].'://'.$parts['host'];
            if(isset($parts['port'])) $url .= ":".$parts['port'];
            if(isset($parts['path'])) $url .= $parts['path'];
        }
        $this->url = $url;
        $this->method = $method??"GET";
        $this->params = $params??[];
        $this->data = $data??[];

        return $this;
    }

    /**
     * 设置请求头
     * @param array $headers
     * @return $this
     */
    public function setHeaders($headers=[]): AHttpService
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * 完全转发http请求
     * @return $this
     */
    public function send(): AHttpService
    {

        $before_time = microtime(true);
        try {

            $client = new Client();
            $response = $client->request($this->method, $this->url, [
                'headers' => $this->headers,
                'query' => $this->params,
                'json' => $this->data,
            ]);
            $res = $response->getBody()->getContents();

            # 记录日志
            $this->log($res,$before_time);

            $this->res_data = $this->outResProcessing($res);

        } catch (GuzzleException|Exception $e) {
            $this->log($e->getMessage(),$before_time);
            $this->res_data = ['code' => 500, 'message' => $e->getMessage()];
        }
        return $this;
    }

    /**
     * 返回数据
     * @return mixed
     */
    public function get()
    {
        return $this->res_data;
    }

    /**
     * 记录日志
     * @param $res
     * @return void
     */
    protected function log($res, $before_time)
    {
        # 如果是json格式则格式化 保留中文和斜杠展示
        if($this->isJson($res)){
            $res = json_decode($res, true);
            $res = json_encode($res, JSON_UNESCAPED_UNICODE);
        }
        $after_time = microtime(true);
        # 记录日志 格式化记录数组
        Log::channel('debug')->info(json_encode([
            '耗时' => round($after_time - $before_time, 3)*1000 . 'ms',
            'url' => $this->url,
            'method' => $this->method,
            'headers' => $this->headers,
            'query' => json_encode($this->params, JSON_UNESCAPED_UNICODE),
            'data' => json_encode($this->data, JSON_UNESCAPED_UNICODE),
            'response' => $res
        ], JSON_UNESCAPED_UNICODE));
    }


    /**
     * 返回值预处理 各种异常处理
     * @param $res
     * @return mixed
     * @throws Exception
     */
    protected function outResProcessing($res)
    {
        // 判断返回字符串是否是json格式
        if (!$this->isJson($res)) {
            throw new Exception($res);
        }

        return json_decode($res, true);
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


