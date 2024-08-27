<?php

namespace hoo\io\common\Command;

use hoo\io\common\Models\CodeObjectModel;
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
        if (!Schema::hasTable('hm_code_object')) {
            Schema::create('hm_code_object', function (Blueprint $table) {
                $table->bigIncrements('id');
                // 字段不为空 不加索引
                $table->string('name',50)->unique('idx_name');
                $table->string('group',50);
                $table->string('label')->nullable();
                $table->text('remark')->nullable();
                $table->longText('object')->nullable();
                $table->dateTime('created_at')->nullable();
                $table->dateTime('updated_at')->nullable();

                $table->index('group','idx_group');
            });
            $this->info('hm_code_object 表创建成功');
            # 放入一个示例
            CodeObjectModel::query()->create([
                'name' => '示例-phpinfo',
                'group' => 'system',
                'label' => '',
                'object' => "<?php phpinfo();",
                ]);
        }
        if (!Schema::hasTable('hm_logs')) {
            Schema::create('hm_logs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('label_a')->nullable();
                $table->string('label_b')->nullable();
                $table->string('label_c')->nullable();
                $table->longText('content')->nullable();
                $table->dateTime('created_at')->nullable();
                $table->dateTime('updated_at')->nullable();

                $table->index('name','idx_name');
                $table->index('label_a','idx_label_a');
                $table->index('label_b','idx_label_b');
                $table->index('label_c','idx_label_c');
            });
            $this->info('hm_logs 表创建成功');
        }

        $this->info('操作成功');
    }
}
