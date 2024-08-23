<?php

namespace hoo\io\common\Command;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class DevCommand extends BaseCommand
{
    # 支持不传递参数
    protected $signature = 'hm:dev {action} {args?*}';

    // Command description
    protected $description = 'hm:dev';

    // Execute the console command
    public function handle()
    {
        $args = $this->argument();
        $arg = $args['args']??[];

        $this->{$args['action']}(...$arg);
    }

    public function test($a='')
    {
        $this->info($a);
        $this->info('test');
    }

    public function runCodeInit()
    {
        # 检查表是否存在
        if (Schema::hasTable('hm_code_object')) {
            $this->error('已存在，无需再建表！');
            return;
        }
        Schema::create('hm_code_object', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->string('label')->unique();
            $table->text('object');
            $table->timestamps();
        });
        $this->info('创建成功');
    }
}
