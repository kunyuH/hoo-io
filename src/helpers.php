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
        return 'https://js.tuguaishou.com/other/hm';
    }
}

if (! function_exists('get_path')) {
    function get_path()
    {
        return __DIR__;
    }
}
