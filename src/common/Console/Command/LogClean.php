<?php
namespace hoo\io\common\Console\Command;

use hoo\io\common\Models\ApiLogModel;
use hoo\io\common\Models\HttpLogModel;
use hoo\io\common\Models\SqlLogModel;
use Illuminate\Support\Facades\Schema;

class LogClean extends BaseCommand
{
    protected $signature = 'hm:LogClean';

	// Command description
	protected $description = '日志清理';

	// Execute the console command
	public function handle()
	{
        # 获取清理时间 即多久之前的日志需清理
        $apiCleanDay = config('hoo-io.HM_API_LOG_CLEAN');
        if (!empty($apiCleanDay) and Schema::hasTable((new ApiLogModel())->getTable())) {
            # 获取日志需清理 的日期
            $apiCleanDate = date('Y-m-d', strtotime('-'.$apiCleanDay.' days')).' 00:00:00';
            # api log 清理
            ApiLogModel::query()->where('created_at', '<', $apiCleanDate)->delete();
        }
        $hhttpCleanDay = config('hoo-io.HM_HHTTP_LOG_CLEAN');
        if (!empty($hhttpCleanDay) and Schema::hasTable((new HttpLogModel())->getTable())) {
            # 获取日志需清理 的日期
            $hhttpCleanDate = date('Y-m-d', strtotime('-'.$hhttpCleanDay.' days')).' 00:00:00';
            # hhttp log 清理
            HttpLogModel::query()->where('created_at', '<', $hhttpCleanDate)->delete();
        }
        $sqlCleanDay = config('hoo-io.HM_SQL_LOG_CLEAN');
        if (!empty($sqlCleanDay) and Schema::hasTable((new SqlLogModel())->getTable())) {
            # 获取日志需清理 的日期
            $sqlCleanDate = date('Y-m-d', strtotime('-'.$sqlCleanDay.' days')).' 00:00:00';
            # sql log 清理
            SqlLogModel::query()->where('created_at', '<', $sqlCleanDate)->delete();
        }
	}
}
