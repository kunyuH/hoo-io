<?php

if (! function_exists('jump_link')) {
    function jump_link($url)
    {
        if(!empty(env('SERVICE_NAME'))){
            return "/".env('SERVICE_NAME')."$url";
        }else{
            return $url;
        }
    }
}

if (! function_exists('get_cdn')) {
    function get_cdn()
    {
//        return 'https://js.tuguaishou.com/other/hm';
        return 'https://ih-patient-v2-api-chongqing.test:8082/hm-r';
    }
}

if (! function_exists('get_path')) {
    function get_path()
    {
        return __DIR__;
    }
}
