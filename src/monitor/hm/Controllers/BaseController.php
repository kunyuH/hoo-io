<?php

namespace hoo\io\monitor\hm\Controllers;

use hoo\io\common\Enums\HttpResponseEnum;
use hoo\io\monitor\hm\Web;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use hoo\io\common\Response\HmBinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BaseController extends Controller
{
    /**
     * @var string 公共布局
     */
    public $layout = "main.layout";

    /**
     * 渲染视图-不带公共布局
     * @param $view
     * @param $data
     * @return string
     */
    public function view($view,$data=[])
    {
        # 字符串替换
        $view = str_replace('.','/',$view);
        $view = str_replace('::','/',$view);

        # 渲染视图
        return View::file(__DIR__ . "/../views/{$view}.blade.php",$data)->render();
    }

    /**
     * 渲染视图-带公共布局
     * @param $view
     * @param $data
     * @return string
     */
    public function v($view,$data=[])
    {
        return $this->view($this->layout,[
            'content' => $this->view($view,$data)
        ]);
    }

    /**
     * 渲染弹出层视图-带公共布局
     * @param $view
     * @param $data
     * @return string
     */
    public function modal($view,$data=[])
    {
        $this->layout = "main.modal";
        return $this->view($this->layout,[
            'content' => $this->view($view,$data)
        ]);
    }

    /**
     * 获取web资源
     * @param $path
     * @return HmBinaryFileResponse
     */
    public function webAsset($path)
    {
        $asset = (new Web())->asset($path);

        if (! $asset) throw new NotFoundHttpException();

        return new HmBinaryFileResponse($asset['path'], 200, [ 'Content-Type' => $asset['mime'] ]);
    }

    /**
     * 成功接口返回
     * @param $data
     * @param $message
     * @param $code
     * @return JsonResponse
     */
    public function resSuccess($data = [],$message='ok',$code = HttpResponseEnum::SUCCESS)
    {
        return (new JsonResponse([
            'code' => $code,
            'message'=>$message,
            'data'=>$data,
        ]));
    }

    /**
     * 失败接口返回
     * @param $data
     * @param $message
     * @param $code
     * @return JsonResponse
     */
    public function resError($data = [],$message='error',$code = HttpResponseEnum::ERROR)
    {
        return (new JsonResponse([
            'code' => $code,
            'message'=>$message,
            'data'=>$data,
        ]));
    }

    /**
     * 执行命令后高亮显示
     * @param $output
     * @return array|string|string[]|null
     */
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
        
        return mb_detect_encoding($str, 'UTF-8', true) === 'UTF-8'?$str:utf8_encode($str);
    }
}
