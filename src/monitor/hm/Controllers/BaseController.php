<?php

namespace hoo\io\monitor\hm\Controllers;

use hoo\io\common\Enums\HttpResponseEnum;
use hoo\io\monitor\hm\Web;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BaseController extends Controller
{
    public $layout = "main.layout";

    public function view($view,$data=[])
    {
        # 字符串替换
        $view = str_replace('.','/',$view);
        $view = str_replace('::','/',$view);
        return View::file(__DIR__ . "/../views/{$view}.blade.php",$data)->render();
    }

    public function v($view,$data=[])
    {
        return $this->view($this->layout,[
            'content' => $this->view($view,$data)
        ]);
    }

    public function webAsset($path)
    {
        $asset = (new Web())->asset($path);

        if (! $asset) throw new NotFoundHttpException();

        return new BinaryFileResponse($asset['path'], 200, [ 'Content-Type' => $asset['mime'] ]);
    }

    public function resSuccess($data = [],$message='ok',$code = HttpResponseEnum::SUCCESS)
    {
        return (new JsonResponse([
            'code' => $code,
            'message'=>$message,
            'data'=>$data,
        ]));
    }
    public function resError($data = [],$message='error',$code = HttpResponseEnum::ERROR)
    {
        return (new JsonResponse([
            'code' => $code,
            'message'=>$message,
            'data'=>$data,
        ]));
    }
}
