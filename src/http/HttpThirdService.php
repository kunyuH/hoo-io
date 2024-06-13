<?php

namespace hoo\io\http;

/**
 * Class HttpService
 * 通用http服务-外部服务
 */
class HttpThirdService extends AHttpService
{
    /**
     * 设置请求头
     * @param array $headers
     * @return $this
     */
    public function setHeaders($headers=[]): AHttpService
    {
        $this->headers = array_merge([
            'Content-Type'=>'application/json'
        ],$headers);

        return $this;
    }
}


