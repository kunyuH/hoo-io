<?php

namespace hoo\io\monitor\hm\Controllers;

use hoo\io\common\Enums\SessionEnum;
use hoo\io\common\Request\HmLoginRequest;
use Illuminate\Http\JsonResponse;
use hoo\io\common\Support\Facade\HooSession;
use Illuminate\Support\Facades\Config;

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
     * @param HmLoginRequest $request
     * @return JsonResponse
     */
    public function login(HmLoginRequest $request)
    {
        $name = $request->input('name');
        $password = $request->input('password');
        
        # 验证账密
        if($name != Config::get('hoo-io.HOO_NAME') || $password != Config::get('hoo-io.HOO_PASSWORD')){
            return $this->resError([],'用户名或密码错误!');
        }

        HooSession::put(SessionEnum::USER_INFO_KEY,[
            'name'=>$name,
        ]);

        return $this->resSuccess([
            'type'=>11,
            'redirect_uri'=>jump_link('/hm/index'),
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
