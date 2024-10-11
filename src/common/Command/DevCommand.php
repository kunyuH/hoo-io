<?php

namespace hoo\io\common\Command;

use hoo\io\http\HHttp;
use hoo\io\monitor\hm\Models\LogicalBlockModel;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class DevCommand extends BaseCommand
{
    # 支持不传递参数
    protected $signature = 'hm:dev {action} {args?*}';
    //hm:dev test x
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

        $clien = new HHttp();
        $resx = $clien->get('https://www.baidu.com');
        $res = $resx->getBody()->getContents();
        print_r($res);
    }

    /**
     * logicalPipelines 模块初始化
     * @return void
     */
    public function logicalPipelinesInit()
    {
        # 检查表是否存在
        if (!Schema::hasTable('hm_logical_block')) {
            Schema::create('hm_logical_block', function (Blueprint $table) {
                $table->integerIncrements('id');
                // 字段不为空 不加索引
                $table->string('object_id',50);
                $table->string('name',50);
                $table->string('group',50);
                $table->string('label')->nullable();
                $table->text('remark')->nullable();
                $table->longText('logical_block')->nullable();
                $table->dateTime('created_at')->nullable();
                $table->dateTime('updated_at')->nullable();
                $table->dateTime('deleted_at')->nullable();

                $table->index('object_id','idx_object_id');
                $table->index('name','idx_name');
                $table->index('group','idx_group');
            });
            $this->info('hm_logical_block 表创建成功');
            # 放入一个示例
            LogicalBlockModel::query()->create([
                'object_id' => 'phpinfo',
                'name' => '示例-phpinfo',
                'group' => 'system',
                'label' => '',
                'logical_block' => "<?php phpinfo();",
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

        if (!Schema::hasTable('hm_logical_pipelines')) {
            Schema::create('hm_logical_pipelines', function (Blueprint $table) {
                $table->integerIncrements('id');
                $table->string('rec_subject_id',50);
                $table->string('name',50);
                $table->string('group',50);
                $table->string('label')->nullable();;
                $table->text('remark')->nullable();
                $table->json('setting')->nullable();
                $table->dateTime('created_at')->nullable();
                $table->dateTime('updated_at')->nullable();
                $table->dateTime('deleted_at')->nullable();

                $table->index('rec_subject_id','idx_rec_subject_id');
                $table->index('name','idx_name');
                $table->index('group','idx_group');
            });
            $this->info('hm_logical_pipelines 表创建成功');
        }

        if (!Schema::hasTable('hm_logical_pipelines_arrange')) {
            Schema::create('hm_logical_pipelines_arrange', function (Blueprint $table) {
                $table->integerIncrements('id');
                $table->integer('logical_pipeline_id');
                $table->integer('logical_block_id')->nullable();
                $table->integer('next_id');
                $table->longText('logical_block')->nullable();
                $table->string('name',50)->nullable();
                $table->enum('type',['custom','common'])->default('common');
                $table->dateTime('created_at')->nullable();
                $table->dateTime('updated_at')->nullable();

                $table->index('logical_pipeline_id','idx_logical_pipeline_id');
                $table->index('logical_block_id','idx_logical_block_id');
                $table->index('next_id','idx_next_id');
                $table->index('type','idx_type');
            });
            $this->info('hm_logical_pipelines_arrange 表创建成功');
        }

        if (!Schema::hasTable('hm_api_log')) {
            Schema::create('hm_api_log', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('app_name',100)->nullable();
                $table->string('user_id',100)->nullable();
                $table->string('domain',50)->nullable();
                $table->string('path',100)->nullable();
                $table->string('method',20)->nullable();
                $table->integer('run_time')->nullable();
                $table->longText('user_agent')->nullable();
                $table->longText('input')->nullable();
                $table->longText('output')->nullable();
                $table->string('status_code',50)->nullable();
                $table->string('ip',50)->nullable();
                $table->dateTime('created_at')->nullable();

                $table->index('user_id','idx_user_id');
                $table->index('path','idx_path');
                $table->index('created_at','idx_created_at');
                $table->index('domain','idx_domain');
                $table->index('method','idx_method');
            });
            $this->info('hm_api_log 表创建成功');
        }

        if (!Schema::hasTable('hm_http_log')) {
            Schema::create('hm_http_log', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('app_name',100)->nullable();
                $table->string('uuid',50)->nullable();
                $table->longText('url')->nullable();
                $table->string('path',200)->nullable();
                $table->string('method')->nullable();
                $table->longText('options')->nullable();
                $table->longText('response')->nullable();
                $table->longText('err')->nullable();
                $table->integer('run_time')->nullable();
                $table->string('run_trace',255)->nullable();
                $table->string('run_path',255)->nullable();
                $table->dateTime('created_at')->nullable();

                $table->index('path','idx_path');
                $table->index('run_path','idx_run_path');
                $table->index('created_at','idx_created_at');
                $table->index('method','idx_method');
            });
            $this->info('hm_http_log 表创建成功');
        }

        $this->info('操作成功');
    }
}
