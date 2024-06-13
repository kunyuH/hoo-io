<?php

namespace hoo\io\http;

/**
 * Class HttpService
 * 通用http服务-内部服务
 */
class HttpInnerService extends AHttpService
{
    # api前缀
    public $api_prefix = '/innerapi';

    /**
     * 设置请求头
     * @param array $headers
     * @return $this
     */
    public function setHeaders($headers=[]): AHttpService
    {
        $this->headers = array_merge([
            'X1-Gp-Color' => request()->header('X1-Gp-Color', ''),
            'Content-Type'=>'application/json'
        ],$headers);

        return $this;
    }

    /**
     * 设置请求参数
     * @param $api
     * @param $method
     * @param $params
     * @param $data
     * @return AHttpService
     */
    public function setReq($api=null, $method=null, $params=null, $data=null): AHttpService
    {
        # 1.剔除无需传递的参数 只保留身份证号account_id
        unset($data['member_id']);
        unset($data['member_info']);

        # 2.剔除无需传递的参数
        unset($params['se_url']);
        unset($params['se_code']);
        unset($params['se_method']);

        return parent::setReq($api,$method,$params,$data);
    }

    /**
     * 取列表返回值第一条
     * @return array|mixed
     */
    public function listFirst()
    {
        /**
         * 符合条件
         * 存在list字段
         */
        if (isset($this->res_data['data']['list'])) {
            return $this->res_data['data']['list'][0] ?? [];
        }
        return $this->res_data;
    }
}


