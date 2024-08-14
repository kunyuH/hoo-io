<?php

namespace hoo\io;
use hoo\io\common\Enums\SessionEnum;
use hoo\io\common\Support\Facade\HooSession;
use hoo\io\database\services\BuilderMacroSql;
use hoo\io\common\Middleware\HooMid;
use hoo\io\monitor\hm\HmRoutes;
use hoo\io\monitor\hm\Middleware\HmAuth;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Query\Builder as QueryBuilder;

class IoServiceProvider extends ServiceProvider
{

    public function boot()
    {
        /**
         * 定义权限 是否登录
         */
        Gate::define('hooAuth', function ($user = null) {
            # true 设置不开启 默认开启
            if(empty(env('HOO_ENABLE',true))){
                return false;
            }
            if(!HooSession::get(SessionEnum::USER_INFO_KEY)){
                return false;
            }
            /**
             * 限制环境
             * local 开发环境可进
             * test 测试环境可进
             * production 生产环境 且请求头中有灰度标识可进
             * 其它环境不可进
             */
            if(!in_array(env('APP_ENV'), ['local', 'test', 'production'])) {
                return false;
            }
            if(env('APP_ENV') == 'production' && request()->header('x1-gp-color') != 'gray') {
                return false;
            }
            return true;
        });

        //注册中间件
        $kernel = $this->app->make(Kernel::class);
        $kernel->pushMiddleware(HooMid::class);
        //添加鉴权中间件
        Route::aliasMiddleware('hoo.auth',HmAuth::class);
    }

    public function register()
    {
        //注册 sql查询服务
        QueryBuilder::macro('getSqlQuery', function () {
            return (new BuilderMacroSql())->getSqlQuery($this);
        });

        //注册 hm  监控 路由
        (new HmRoutes())->registerWebRoutes();
    }
}
