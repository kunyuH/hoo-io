<?php

namespace hoo\io\monitor\hm\Controllers;

use hoo\io\common\Enums\SessionEnum;
use hoo\io\monitor\hm\Web;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use hoo\io\common\Support\Facade\HooSession;


class IndexController extends BaseController
{
    /**
     * 首页
     * @return string
     */
    public function index()
    {
        return $this->v('index.index');
    }

    public function sendCommand(Request $request)
    {
        $command = $request->input('command');
        
        $output = [];
        $return_var = 0;
        exec('php ../artisan '.$command,$output, $return_var);

        return $this->resSuccess([
            'open_type'=>1,
            'type'=>7,
        ],$this->execOutput($output));
    }

    public function execOutput($output)
    {
        foreach ($output as $kty=>&$vo){
            if(empty($vo)){
                unset($output[$kty]);
            }
        }
        $str = implode('<br>',$output);
        $str = str_replace('INFO','<span style="color: #009933;">INFO</span>',$str);
        $str = str_replace('ERROR','<span style="color: #ff0000;">ERROR</span>',$str);
        $str = str_replace('DEBUG','<span style="color: #0066ff;">DEBUG</span>',$str);
        $str = str_replace('NOTICE','<span style="color: #ff9900;">NOTICE</span>',$str);
        $str = str_replace('ALERT','<span style="color: #ff0000;">ALERT</span>',$str);
        $str = str_replace('CRITICAL','<span style="color: #ff0000;">CRITICAL</span>',$str);
        $str = str_replace('WARNING','<span style="color: #ff9900;">WARNING</span>',$str);
        $str = str_replace('EMERGENCY','<span style="color: #ff0000;">EMERGENCY</span>',$str);
        $str = str_replace('E_NOTICE','<span style="color: #ff9900;">E_NOTICE</span>',$str);
        $str = str_replace('E_WARNING','<span style="color: #ff9900;">E_WARNING</span>',$str);
        $str = str_replace('E_PARSE','<span style="color: #ff0000;">E_PARSE</span>',$str);
        $str = str_replace('E_ERROR','<span style="color: #ff0000;">E_ERROR</span>',$str);
        $str = str_replace('E_CORE_ERROR','<span style="color: #ff0000;">E_CORE_ERROR</span>',$str);
        $str = str_replace('E_CORE_WARNING','<span style="color: #ff9900;">E_CORE_WARNING</span>',$str);
        $str = str_replace('E_COMPILE_ERROR','<span style="color: #ff0000;">E_COMPILE_ERROR</span>',$str);
        $str = str_replace('E_COMPILE_WARNING','<span style="color: #ff9900;">E_COMPILE_WARNING</span>',$str);
        $str = str_replace('E_USER_ERROR','<span style="color: #ff0000;">E_USER_ERROR</span>',$str);
        $str = str_replace('E_USER_WARNING','<span style="color: #ff9900;">E_USER_WARNING</span>',$str);
        $str = str_replace('E_USER_NOTICE','<span style="color: #ff9900;">E_USER_NOTICE</span>',$str);
        $str = str_replace('E_STRICT','<span style="color: #ff9900;">E_STRICT</span>',$str);
        $str = str_replace('E_RECOVERABLE_ERROR','<span style="color: #ff0000;">E_RECOVERABLE_ERROR</span>',$str);
        $str = str_replace('E_DEPRECATED','<span style="color: #ff9900;">E_DEPRECATED</span>',$str);
        $str = str_replace('DONE','<span style="color: rgb(0, 187, 0);">DONE</span>',$str);

        # 方括号内加粗
        $str = preg_replace('/\[(.*?)\]/','<b>[$1]</b>',$str);

        return $str;
    }
}
