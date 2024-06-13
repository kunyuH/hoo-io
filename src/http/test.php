<?php


use hoo\io\http\HttpInnerService;

class Test
{
    /**
     * @throws Exception
     */
    public function test()
    {
        $service = config('http_service.inner_service');
        $api = '/innerapi/xxx';
        return (new HttpInnerService($service))
            ->setReq($api,'GET')
            ->send()
            ->get();
    }
}

$test = new Test();
$test->test();