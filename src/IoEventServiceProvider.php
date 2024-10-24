<?php

namespace hoo\io;

use hoo\io\common\Listeners;
use Illuminate\Database\Events\QueryExecuted;
use hoo\io\common\Listeners\DatabaseExecuteLogListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class IoEventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        QueryExecuted::class => [
            DatabaseExecuteLogListener::class
        ]
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
