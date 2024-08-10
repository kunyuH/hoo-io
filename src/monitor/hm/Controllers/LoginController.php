<?php

namespace hoo\io\monitor\hm\Controllers;

use hoo\io\common\Enums\SessionEnum;
use hoo\io\monitor\hm\Web;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use hoo\io\common\Support\Facade\HooSession;

class LoginController extends BaseController
{
    /**
     * 登录页
     * @return string
     */
    public function index()
    {
        return $this->view('login.index',[
            'session_id' => HooSession::getId(),
            'token' => HooSession::get('a')
        ]);
    }

    /**
     * 登录
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $name = $request->input('name');
        $password = $request->input('password');

        # 验证账密
        if($name != env('HOO_NAME') || $password != env('HOO_PASSWORD')){
            return $this->resError([],'用户名或密码错误!');
        }

        HooSession::put(SessionEnum::USER_INFO_KEY,[
            'name'=>$name,
        ]);

        return $this->resSuccess([
            'type'=>11,
            'redirect_uri'=>'/hm/index',
        ]);
    }

    /**
     * 退出登录
     * @return JsonResponse
     */
    public function logout()
    {
        HooSession::remove(SessionEnum::USER_INFO_KEY);
        return $this->resSuccess([],'退出成功!');
    }
}
