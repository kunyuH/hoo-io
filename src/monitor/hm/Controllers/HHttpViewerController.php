<?php
namespace hoo\io\monitor\hm\Controllers;

use hoo\io\common\Models\HttpLogModel;
use hoo\io\monitor\hm\Request\HhttpLogViewerRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HHttpViewerController extends BaseController
{
    public function index(HhttpLogViewerRequest $request)
    {
        $path = $request->input('path');
        $options = $request->input('options');
        $response = $request->input('response');
        $hoo_traceid = $request->input('hoo_traceid');
        $run_path = $request->input('run_path');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        if(empty($start_date)){
            # 获取7天前时间
            $start_date = date('Y-m-d',strtotime('-7 days'));
        }
        if(empty($end_date)){
            $end_date = date('Y-m-d');
        }

        $hHttpLogList = HttpLogModel::query()
//            ->with(['HttpLog'])
            ->when(!empty($path),function (Builder $q) use ($path){
                $q->where('path','like','%'.$path.'%');
            })
            ->when(!empty($options),function (Builder $q) use ($options){
                $q->where('options','like','%'.$options.'%');
            })
            ->when(!empty($response),function (Builder $q) use ($response){
                $q->where('response','like','%'.$response.'%');
            })
            ->when(!empty($run_path),function (Builder $q) use ($run_path){
                $q->where('run_path','like','%'.$run_path.'%');
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

        # 获取7天前时间
        $sevenDaysAgo = date('Y-m-d H:i:s',strtotime('-7 days'));

        # 获取近7日天访问量 与 平均性能
        $sevenVisits = Cache::remember('sevenVisits',60*60, function () use ($sevenDaysAgo){
            return HttpLogModel::query()
                ->select(
                    DB::raw('count(*) as count'),
                    DB::raw('avg(run_time) as avg')
                )
                ->whereBetween('created_at',[$sevenDaysAgo,date('Y-m-d H:i:s')])
                ->first();
        });

        return $this->v('HHttpViewer.index',[
            'sevenVisits'=>$sevenVisits,
            'hHttpLogList'=>$hHttpLogList,
            'start_date'=>$start_date,
            'end_date'=>$end_date,
        ]);
    }

    /**
     * 根据path统计
     * @param HhttpLogViewerRequest $request
     * @return string
     */
    public function serviceStatisticsItem(HhttpLogViewerRequest $request)
    {
        # 获取7天前时间
        $sevenDaysAgo = date('Y-m-d H:i:s',strtotime('-7 days'));

        $hHttpLogStatisticsList = Cache::remember('serviceStatisticsItem',60*60, function () use ($sevenDaysAgo){
            # 获取近7日path访问统计
            return HttpLogModel::query()
                ->select('path',
                    DB::raw('count(*) as count'),
                    DB::raw('avg(run_time) as avg'))
                ->whereBetween('created_at',[$sevenDaysAgo,date('Y-m-d H:i:s')])
                ->groupBy('path')
                ->orderBy('count','desc')
                ->get();
        });

        return $this->modal('HHttpViewer.serviceStatisticsItem',[
            'hHttpLogStatisticsList'=>$hHttpLogStatisticsList,
        ]);
    }

    public function sevenVisitsItem(HhttpLogViewerRequest $request)
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

        $hhttpLogList = HttpLogModel::query()
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

        return $this->modal('HHttpViewer.sevenVisitsItem',[
            'hhttpLogList'=>$hhttpLogList,
            'startDate'=>$startDate,
            'endDate'=>$endDate,
        ]);
    }


    public function details(HhttpLogViewerRequest $request)
    {
        $id = $request->input('id');

        $hHttpLogList = HttpLogModel::query()
            ->where('id', '=', $id)
            ->first();

        return $this->modal('HHttpViewer.details', [
            'hHttpLogList' => $hHttpLogList
        ]);
    }
}
