<?php

namespace hoo\io\monitor\hm\Controllers;

use hoo\io\common\Enums\SessionEnum;
use hoo\io\common\Request\HmLoginRequest;
use Illuminate\Http\JsonResponse;
use hoo\io\common\Support\Facade\HooSession;
use Illuminate\Support\Facades\Cache;
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

        # 从key上限制每天刷新登录失败次数
        $check_login_num_key = 'hoo_login_'.date('Ymd');
        $login_error_num = (int)Cache::get($check_login_num_key);
        if($login_error_num >= Config::get('hoo-io.HOO_LOGIN_RETRY')){
            return $this->resError([],'登录失败次数过多，请稍后再试！');
        }

        # 验证账密
        if($name != Config::get('hoo-io.HOO_NAME') || $password != Config::get('hoo-io.HOO_PASSWORD')){

            # 记录错误次数 无视客户端 无视账号更安全
            Cache::put($check_login_num_key,$login_error_num+1,86400);

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
