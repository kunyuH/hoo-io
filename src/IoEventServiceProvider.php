<?php

namespace hoo\io;

use Illuminate\Database\Events\QueryExecuted;
use hoo\io\common\Listeners\DatabaseExecuteLogListener;
use hoo\io\common\Listeners\LogRequestHandledListener;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class IoEventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        # 监听数据库查询事件
        QueryExecuted::class => [
            DatabaseExecuteLogListener::class
        ],
        # 监听请求结束事件
        RequestHandled::class => [
            LogRequestHandledListener::class
        ]
        # 监听命令执行后事件
        # Artisan\Console\Events\CommandFinished::class => [
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
