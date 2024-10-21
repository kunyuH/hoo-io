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
        $hoo_traceid = $request->input('hoo_traceid');
        $run_path = $request->input('run_path');

        $hHttpLogList = HttpLogModel::query()
//            ->with(['HttpLog'])
            ->when(!empty($path),function (Builder $q) use ($path){
                $q->where('path','=',$path);
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

        # 获取近7日天访问量
        $sevenVisits = Cache::remember('sevenVisits',60*60, function () use ($sevenDaysAgo){
            return HttpLogModel::query()
                ->whereBetween('created_at',[$sevenDaysAgo,date('Y-m-d H:i:s')])
                ->count();
        });
        # 获取近7日平均性能
        $sevenAveragePer = Cache::remember('sevenVisits',60*60, function () use ($sevenDaysAgo){
            return HttpLogModel::query()
                ->whereBetween('created_at',[$sevenDaysAgo,date('Y-m-d H:i:s')])
                ->avg('run_time');
        });

        return $this->v('HHttpViewer.index',[
            'sevenVisits'=>$sevenVisits,
            'sevenAveragePer'=>$sevenAveragePer,
            'hHttpLogList'=>$hHttpLogList,
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


    public function details(HhttpLogViewerRequest $request)
    {
        $id = $request->input('id');

        $hHttpLogList = HttpLogModel::query()
//            ->with(['HttpLog'])
            ->where('id', '=', $id)
            ->first();

        return $this->modal('HHttpViewer.details', [
            'hHttpLogList' => $hHttpLogList
        ]);
    }
}
