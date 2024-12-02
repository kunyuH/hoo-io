<?php
namespace hoo\io\monitor\hm\Controllers;

use hoo\io\common\Models\ApiLogModel;
use hoo\io\common\Models\HttpLogModel;
use hoo\io\common\Models\SqlLogModel;
use hoo\io\common\Support\Facade\HooSession;
use hoo\io\monitor\hm\Request\LogViewerRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LogViewerController extends BaseController
{
    public function index(LogViewerRequest $request)
    {
        $path = $request->input('path');
        $input = $request->input('input');
        $output = $request->input('output');
        $user_id = $request->input('user_id');
        $hoo_traceid = $request->input('hoo_traceid');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        if(empty($start_date)){
            # 获取当天前时间
            $start_date = date('Y-m-d');
        }
        if(empty($end_date)){
            $end_date = date('Y-m-d');
        }

        $apiLogList = ApiLogModel::query()
            ->with(['HttpLog'])
            ->when(!empty($path),function (Builder $q) use ($path){
                $q->where('path','like','%'.$path.'%');
            })
            ->when(!empty($input),function (Builder $q) use ($input){
                $q->where('input','like','%'.$input.'%');
            })
            ->when(!empty($output),function (Builder $q) use ($output){
                $q->where('output','like','%'.$output.'%');
            })
            ->when(!empty($user_id),function (Builder $q) use ($user_id){
                $q->where('user_id','=',$user_id);
            })
            ->when(!empty($hoo_traceid),function (Builder $q) use ($hoo_traceid){
                $q->where('hoo_traceid','=',$hoo_traceid);
            })
            ->when(!empty($start_date),function (Builder $q) use ($start_date){
                $start_date = $start_date.' 00:00:00';
                $q->where('created_at','>=',$start_date);
            })
            ->when(!empty($end_date),function (Builder $q) use ($end_date){
                $end_date = $end_date.' 23:59:59';
                $q->where('created_at','<=',$end_date);
            })
            ->orderBy('id','desc')
            ->paginate(20);

        return $this->v('logViewer.index',[
            'apiLogList'=>$apiLogList,
            'start_date'=>$start_date,
            'end_date'=>$end_date,
        ]);
    }

    public function sevenVisits(LogViewerRequest $request)
    {
        # 获取7天前时间
        $sevenDaysAgo = date('Y-m-d H:i:s',strtotime('-7 days'));
        # 获取近7日天访问量 与 平均性能
        $apiSevenVisits = Cache::remember('apiSevenVisits',60*60, function () use ($sevenDaysAgo){
            return ApiLogModel::query()
                ->select(
                    DB::raw('count(*) as count'),
                    DB::raw('ROUND(avg(run_time),2) as avg')
                )
                ->whereBetween('created_at',[$sevenDaysAgo,date('Y-m-d H:i:s')])
                ->first();
        });
        return $this->resSuccess($apiSevenVisits);
    }

    public function sevenVisitsItem(LogViewerRequest $request)
    {
        $path = $request->input('path');
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        if(empty($startDate)){
            # 获取7天前时间
            $startDate = date('Y-m-d',strtotime('-7 days'));
        }
        if(empty($endDate)){
            $endDate = date('Y-m-d');
        }

        $apiLogList = ApiLogModel::query()
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') AS date"),
                DB::raw('count(1) as count'),
                DB::raw('avg(run_time) as avg')
            )
            ->when(!empty($path),function (Builder $q) use ($path){
                $q->where('path','=',$path);
            })
            ->when(!empty($startDate),function (Builder $q) use ($startDate){
                $startDate = $startDate.' 00:00:00';
                $q->where('created_at','>=',$startDate);
            })
            ->when(!empty($endDate),function (Builder $q) use ($endDate){
                $endDate = $endDate.' 23:59:59';
                $q->where('created_at','<=',$endDate);
            })
            ->orderBy('date','desc')
            # 按照日期分组
            ->groupBy( DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d')"))
            ->paginate(20);

        return $this->modal('logViewer.sevenVisitsItem',[
            'apiLogList'=>$apiLogList,
            'startDate'=>$startDate,
            'endDate'=>$endDate,
        ]);
    }

    /**
     * api带宽情况
     * @param LogViewerRequest $request
     * @return string
     */
    public function bandwidthStatisticsItem(LogViewerRequest $request)
    {
        $orderBy = $request->input('orderBy','out_byte');

        $path = $request->input('path');
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        if(empty($startDate)){
            # 获取7天前时间
            $startDate = date('Y-m-d',strtotime('-7 days'));
        }
        if(empty($endDate)){
            $endDate = date('Y-m-d');
        }

        $apiLogList = ApiLogModel::query()
            ->select(
                'path',
                DB::raw('count(*) as count'),
                DB::raw('ROUND(avg(CHAR_LENGTH(input)),2) as in_byte'),
                DB::raw('ROUND(avg(CHAR_LENGTH(output)),2) as out_byte')
            )
            ->when(!empty($path),function (Builder $q) use ($path){
                $q->where('path','=',$path);
            })
            ->when(!empty($startDate),function (Builder $q) use ($startDate){
                $startDate = $startDate.' 00:00:00';
                $q->where('created_at','>=',$startDate);
            })
            ->when(!empty($endDate),function (Builder $q) use ($endDate){
                $endDate = $endDate.' 23:59:59';
                $q->where('created_at','<=',$endDate);
            })
            # 按照日期分组
            ->groupBy('path')
            ->orderBy($orderBy,'desc')
            ->paginate(20);

        return $this->modal('logViewer.bandwidthStatisticsItem',[
            'apiLogList'=>$apiLogList,
            'path'=>$path,
            'startDate'=>$startDate,
            'endDate'=>$endDate,
            'orderBy'=>$orderBy,
        ]);
    }
    /**
     * 根据path统计
     * @param LogViewerRequest $request
     * @return string
     */
    public function serviceStatisticsItem(LogViewerRequest $request)
    {
        # 获取7天前时间
        $sevenDaysAgo = date('Y-m-d H:i:s',strtotime('-7 days'));

        # 获取近7日path访问统计
        $apiLogStatisticsList = Cache::remember('apiLogStatisticsList',60*60, function () use ($sevenDaysAgo){
            return  ApiLogModel::query()
                ->select('path',
                    DB::raw('count(*) as count'),
                    DB::raw('avg(run_time) as avg'))
                ->whereBetween('created_at',[$sevenDaysAgo,date('Y-m-d H:i:s')])
                ->groupBy('path')
                ->orderBy('count','desc')
                ->get();
        });

        return $this->modal('logViewer.serviceStatisticsItem',[
            'apiLogStatisticsList'=>$apiLogStatisticsList,
        ]);
    }

    public function details(LogViewerRequest $request)
    {
        $id = $request->input('id');

        $apiLog = ApiLogModel::query()
            ->with(['HttpLog','SqlLog'])
            ->where('id','=',$id)
            ->first();

        return $this->modal('logViewer.details',[
            'apiLog'=>$apiLog
        ]);
    }

    public function showLog(LogViewerRequest $request)
    {
        $path = $request->input('path');
        $path = urldecode($path);
        # 设置当前日志根目录
        HooSession::put('log-viewer.storage-path',$path);

        return $this->resSuccess([
            'open_type'=>3,
            'redirect_uri'=>jump_link('/'.config('log-viewer.route.attributes.prefix','log-viewer')).'/logs',
        ]);
    }

    /**
     * 日志磁盘占用情况
     * @param LogViewerRequest $request
     * @return void
     */
    public function diskUsage(LogViewerRequest $request)
    {
        # 查看磁盘占用情况
        Cache::forget('diskUsage');
        $diskUsage = Cache::remember('diskUsage',60*60, function () {
            $api_log_table_name = (new ApiLogModel())->getTableName();
            $hhttp_log_table_name = (new HttpLogModel())->getTableName();
            $database_log_table_name = (new SqlLogModel())->getTableName();
            $sql = "SELECT
                        TABLE_SCHEMA as `Database`,
                        TABLE_NAME as `Table`,
                        ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) AS `Size_MB`
                    FROM
                        information_schema.TABLES
                    WHERE
                         TABLE_NAME in ('{$api_log_table_name}','{$hhttp_log_table_name}','{$database_log_table_name}')
                    ORDER BY `Table` desc
                    ";

            $diskUsage = DB::connection()->select($sql);
            $diskUsage = collect($diskUsage)->map(function ($item) {
                $item->Size_MB = number_format($item->Size_MB, 2);
                return $item;
            });
            # 数据按照Table分组
            $diskUsage = $diskUsage->groupBy('Table');
            return $diskUsage;
        });
        return $this->resSuccess($diskUsage);
    }
}
