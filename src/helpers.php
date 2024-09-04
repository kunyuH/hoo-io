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
        return 'https://js.tuguaishou.com/other';
    }
}

if (! function_exists('get_path')) {
    function get_path()
    {
        return __DIR__;
    }
}

if (! function_exists('get_files')) {
    /**
     * 获取目录内所有文件，包括子目录内文件
     * @param $dir
     * @return array
     */
    function get_files($dir) {
        $files = [];

        if (is_dir($dir)) { // 判断目录是否存在
            $handle = opendir($dir);

            while (($file = readdir($handle)) !== false) {
                if ($file != "." && $file != "..") {
                    $path = $dir . "/" . $file;
                    if (is_dir($path)) { // 如果是目录，递归调用
                        $files = array_merge($files, get_files($path));
                    } else { // 如果是文件，添加到结果数组中
                        $files[] = $path;
                    }
                }
            }

            closedir($handle); // 关闭目录句柄
        }

        return $files;
    }
}


