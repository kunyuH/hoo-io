<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

if (! function_exists('jump_link')) {
    function jump_link($url)
    {
        if(!empty(Config::get('hoo-io.SERVICE_NAME'))){
            return "/".Config::get('hoo-io.SERVICE_NAME')."$url";
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
if (! function_exists('ho_uuid')) {
    /**
     * 生成UUID
     * @param $prefix
     * @return string
     */
    function ho_uuid($prefix = "")
    {
        $str = md5(uniqid(mt_rand(), true));
        $uuid = substr($str, 0, 8) . '-';
        $uuid .= substr($str, 8, 4) . '-';
        $uuid .= substr($str, 12, 4) . '-';
        $uuid .= substr($str, 16, 4) . '-';
        $uuid .= substr($str, 20, 12);

        return $prefix . $uuid;
    }
}

if (! function_exists('is_dictionary')) {
    /**
     * 判断是否是字典
     * @param $array
     * @return bool
     */
    function is_dictionary($array)
    {
        if (!is_array($array) || empty($array)) {
            return false;
        }
        if (array_keys($array) !== range(0, count($array) - 1)) {
            return true;
        }
        return false;
    }
}
if (! function_exists('ho_fnmatchs')) {
    /**
     * fnmatch 匹配
     * @param $pattern
     * @param $filename
     * @return bool
     */
    function ho_fnmatchs($pattern, $filename)
    {
        if (is_string($filename)) {
            return fnmatch($pattern, $filename);
        } elseif (is_array($filename)) {
            foreach ($filename as $v) {
                if (fnmatch($pattern, $v)) {
                    return true;
                }
            }
        }
        return false;
    }
}
if (! function_exists('is_json')) {
    /**
     * 判断是否是json格式
     * @param $str
     * @return bool
     */
    function is_json($str)
    {
        if (!is_string($str)) {
            return false;
        }
        // 判断返回字符串是否是json格式
        if (is_null(json_decode($str, true))) {
            return false;
        }
        return true;
    }
}
if (! function_exists('get_run_trace')) {
    /**
     * 获取代码调用位置【vendor目录内的调用会被忽略】
     * 用于内部依赖第三方接口 或 数据库 调用时 调用时代码位置
     * @return string
     */
    function get_run_trace()
    {
        $run_trace = '';
        # 获取vendor 位置
        $vendorPath = base_path('vendor');
        foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) as $trace) {
            if(strpos($trace['file']??'',$vendorPath) === false){
               if(!isset($trace['file']) or !isset($trace['line'])){
                   continue;
               }
                # 获取当前应用所在根目录
                $file = str_replace(base_path(), '', $trace['file']);
                $run_trace = $file.':'.$trace['line'];
                break;
            }
        }
        return $run_trace;
    }
}
if (! function_exists('get_run_path')) {
    /**
     * 获取发起http请求 或 执行sql的路径 或 命令
     * @return string
     */
    function get_run_path()
    {
        $runPath = '';
        if (App::runningInConsole()){
            $runPath = implode(' ', $_SERVER['argv']??[]);
        }else{
            $runPath = request()->path();
        }
        return $runPath;
    }
}
if (! function_exists('in_string')) {
    /**
     * 判断字符串中是否包含某几个字符
     * @return string
     */
    function in_string($chars=[],$string)
    {
        foreach ($chars as $char) {
            if (strpos($string, $char) !== false) {
                return true; // 如果找到了字符集中的字符，返回true
            }
        }
        return false; // 如果没有找到字符集中的字符，返回false
    }
}

if (! function_exists('get_chinese_weekday')) {
    /**
     * 根据日期展示是星期几
     * @param $date
     * @return string
     */
    function get_chinese_weekday($date) {
        # 如果长度超过10 则截取前10位
        if(strlen($date) > 10){
            $date = substr($date,0,10);
        }
        $weekdayNumber = date('w', strtotime($date));
        $weekdays = ['日', '一', '二', '三', '四', '五', '六'];
        return '星期' . $weekdays[$weekdayNumber];
    }
}
if (! function_exists('get_directory_tree')) {
    function get_directory_tree($directory, $allowed_extensions = []) {
        $folders = [];
        $files = [];

        // 遍历目录
        foreach (scandir($directory) as $file) {
            if ($file == '.' || $file == '..') continue;

            $path = $directory . '/' . $file;
            if (is_dir($path)) {
                // 如果是目录，递归获取子目录内容
                $folders[$file] = get_directory_tree($path, $allowed_extensions);
            } else {
                // 如果是文件，检查文件后缀是否在允许的扩展名列表中
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                if (empty($allowed_extensions) || in_array($extension, $allowed_extensions)) {
                    $files[] = $file;
                }
            }
        }

        // 合并文件夹和文件列表，文件夹在前，文件在后
        return array_merge($folders, $files);
    }
}




