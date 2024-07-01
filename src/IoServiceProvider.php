<?php

namespace hoo\io;
use hoo\io\database\services\BuilderMacroSql;
use hoo\io\monitor\clockwork\Middleware\ClockworkMid;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Query\Builder as QueryBuilder;

class IoServiceProvider extends ServiceProvider
{

    public function boot()
    {
        # 注册-定义Clockwork可查看权限
        Gate::define('viewClockwork', function ($user = null) {

            /**
             * 验证token
             */
            # 判断授权码是否正确
            $get_token = function (){
                if(!empty($token = request()->get('token', ''))){
                    return $token;
                }
                return Session::get('clockwork_auth_token', '');
            };

            $token = $get_token();
            if($token !== env('CLOCKWORK_AUTH_TOKEN')) {
                return false;
            }
            Session::put('clockwork_auth_token', $token);

            /**
             * 限制环境
             * local 开发环境可进
             * test 测试环境可进
             * production 生产环境 且请求头中有灰度标识可进
             * 其它环境不可进
             */
            if(env('APP_ENV') == 'local') {
                return true;
            }elseif(env('APP_ENV') == 'test') {
                return true;
            }elseif(env('APP_ENV') == 'production') {
                if (request()->header('x1-gp-color') == 'gray') {
                    return true;
                }
            }
            return false;
        });

        //注册中间件
        $kernel = $this->app->make(Kernel::class);
        $kernel->pushMiddleware(ClockworkMid::class);
    }

    public function register()
    {
        //注册 sql查询服务
        QueryBuilder::macro('getSqlQuery', function () {
            return (new BuilderMacroSql())->getSqlQuery($this);
        });
    }
}
