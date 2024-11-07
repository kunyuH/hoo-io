<?php

namespace hoo\io\common\Console;

use Clockwork\Support\Laravel\ClockworkCleanCommand;
use Illuminate\Console\Scheduling\Schedule;
  
class Kernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function schedule(Schedule $schedule)
    {
        try {
            // 本定时任务的作用是清理过期日志文件
            // 具体时间配置见env配置中的CLOCKWORK_STORAGE_EXPIRATION项 默认7天
            $schedule->command(ClockworkCleanCommand::class)->everyMinute();
        }catch (\Throwable $e){}
    }
}
