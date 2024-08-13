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
