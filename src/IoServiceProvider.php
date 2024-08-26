<?php

namespace hoo\io;

use hoo\io\common\Command\DevCommand;
use hoo\io\common\Command\RunCodeCommand;
use hoo\io\common\Enums\SessionEnum;
use hoo\io\common\Support\Facade\HooSession;
use hoo\io\database\services\BuilderMacroSql;
use hoo\io\common\Middleware\HooMid;
use hoo\io\monitor\hm\Controllers\CodeController;
use hoo\io\monitor\hm\Controllers\IndexController;
use hoo\io\monitor\hm\Controllers\LoginController;
use hoo\io\monitor\hm\Middleware\HmAuth;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Query\Builder as QueryBuilder;

class IoServiceProvider extends ServiceProvider
{

    public function boot()
    {
        /**
         * 注册权限
         */
        $this->registerAuth();

        /**
         * 注册中间件
         */
        $this->registerMiddleware();

    }

    public function register()
    {
        //注册 sql查询服务
        QueryBuilder::macro('getSqlQuery', function () {
            return (new BuilderMacroSql())->getSqlQuery($this);
        });

        /**
         * 注册 hm  监控 路由
         */
        $this->registerWebRoutes();

        /**
         * 注册命令
         */
        $this->registerCommands();
    }

    /**
     * 注册权限
     * @return void
     */
    public function registerAuth()
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
    }

    /**
     * 注册中间件
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function registerMiddleware()
    {
        //注册中间件-默认运行
        $kernel = $this->app->make(Kernel::class);
        $kernel->pushMiddleware(HooMid::class);
        //注册中间件-路由中引用执行【鉴权中间件】
        Route::aliasMiddleware('hoo.auth',HmAuth::class);
    }

    /**
     * 注册命令
     * @return void
     */
    public function registerCommands()
    {
        //注册命令
        if ($this->app->runningInConsole()){
            $this->commands([
                //命令
                RunCodeCommand::class,
                DevCommand::class,
            ]);
        }
    }

    /**
     * 注册 hm  监控 路由
     * @return void
     */
    public function registerWebRoutes()
    {
        Route::prefix('hm')->group(function (){

            Route::post('login', [LoginController::class,'login']);
            Route::post('logout', [LoginController::class,'logout']);
            Route::prefix('login')->group(function (){
                Route::get('index',[LoginController::class,'index']);
            });

            Route::middleware('hoo.auth')->group(function (){

                Route::get('index',[IndexController::class,'index']);
                Route::post('send-command',[IndexController::class,'sendCommand']);

                Route::get('run-command',[IndexController::class,'runCommand']);
                Route::post('run-command',[IndexController::class,'runCommand']);

                Route::get('run-code',[IndexController::class,'runCode']);
                Route::post('run-code',[IndexController::class,'runCode']);

                Route::prefix('code')->group(function (){
                    Route::get('index',[CodeController::class,'index']);
                    Route::get('list',[CodeController::class,'list']);
                    Route::get('details',[CodeController::class,'details']);
                    Route::post('save',[CodeController::class,'save']);
                });
            });
        });

        Route::prefix('hm-r')->group(function (){
            Route::get('{path}',[IndexController::class,'webAsset'])->where('path', '.+');
        });

        Route::fallback(function () {
            return response()->json([
                'code'    => 404,
                'message' => 'Not Found!',
                'data'    => [],
            ]);
        });
    }
}
