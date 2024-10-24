<?php
namespace hoo\io\monitor\hm\Controllers;

use hoo\io\common\Models\SqlLogModel;
use hoo\io\monitor\hm\Request\SqlLogViewerRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SqlViewerController extends BaseController
{
    public function index(SqlLogViewerRequest $request)
    {
        $hoo_traceid = $request->input('hoo_traceid');
        $connection_name = $request->input('connection_name');
        $database = $request->input('database');
        $run_path = $request->input('run_path');

        $logList = SqlLogModel::query()
            ->when(!empty($database),function (Builder $q) use ($database){
                $q->where('database','=',$database);
            })
            ->when(!empty($connection_name),function (Builder $q) use ($connection_name){
                $q->where('connection_name','=',$connection_name);
            })
            ->when(!empty($hoo_traceid),function (Builder $q) use ($hoo_traceid){
                $q->where('hoo_traceid','=',$hoo_traceid);
            })
            ->when(!empty($run_path),function (Builder $q) use ($run_path){
                $q->where('run_path','=',$run_path);
            })
            ->orderBy('id','desc')
            ->paginate(20);

        # 获取7天前时间
        $sevenDaysAgo = date('Y-m-d H:i:s',strtotime('-7 days'));

        # 获取近7日天访问量 与 平均性能
        $sevenVisits = Cache::remember('sqlSevenVisits',60*60, function () use ($sevenDaysAgo){
            return SqlLogModel::query()
                ->select(
                    DB::raw('count(*) as count'),
                    DB::raw('avg(run_time) as avg')
                )
                ->whereBetween('created_at',[$sevenDaysAgo,date('Y-m-d H:i:s')])
                ->first();
        });

        return $this->v('SqlViewer.index',[
            'sevenVisits'=>$sevenVisits,
            'logList'=>$logList,
        ]);
    }

    /**
     * 根据database统计
     * @param SqlLogViewerRequest $request
     * @return string
     */
    public function serviceStatisticsItem(SqlLogViewerRequest $request)
    {
        # 获取7天前时间
        $sevenDaysAgo = date('Y-m-d H:i:s',strtotime('-7 days'));

        $logStatisticsList = Cache::remember('sqlServiceStatisticsItem',60*60, function () use ($sevenDaysAgo){
            # 获取近7日path访问统计
            return SqlLogModel::query()
                ->select('database',
                    DB::raw('count(*) as count'),
                    DB::raw('avg(run_time) as avg'))
                ->whereBetween('created_at',[$sevenDaysAgo,date('Y-m-d H:i:s')])
                ->groupBy('database')
                ->orderBy('count','desc')
                ->get();
        });

        return $this->modal('SqlViewer.serviceStatisticsItem',[
            'logStatisticsList'=>$logStatisticsList,
        ]);
    }


    public function details(SqlLogViewerRequest $request)
    {
        $id = $request->input('id');

        $logList = SqlLogModel::query()
            ->where('id', '=', $id)
            ->first();

        return $this->modal('SqlViewer.details', [
            'logList' => $logList
        ]);
    }
}
