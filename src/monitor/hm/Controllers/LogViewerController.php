<?php
namespace hoo\io\monitor\hm\Controllers;

use hoo\io\common\Models\ApiLogModel;
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
        $user_id = $request->input('user_id');
        $hoo_traceid = $request->input('hoo_traceid');

        $apiLogList = ApiLogModel::query()
            ->with(['HttpLog'])
            ->when(!empty($path),function (Builder $q) use ($path){
                $q->where('path','=',$path);
            })
            ->when(!empty($user_id),function (Builder $q) use ($user_id){
                $q->where('user_id','=',$user_id);
            })
            ->when(!empty($hoo_traceid),function (Builder $q) use ($hoo_traceid){
                $q->where('hoo_traceid','=',$hoo_traceid);
            })
            ->orderBy('id','desc')
            ->paginate(20);

        # 获取7天前时间
        $sevenDaysAgo = date('Y-m-d H:i:s',strtotime('-7 days'));

        # 获取近7日天访问量 与 平均性能
        $apiSevenVisits = Cache::remember('apiSevenVisits',60*60, function () use ($sevenDaysAgo){
            return ApiLogModel::query()
                ->select(
                    DB::raw('count(*) as count'),
                    DB::raw('avg(run_time) as avg')
                )
                ->whereBetween('created_at',[$sevenDaysAgo,date('Y-m-d H:i:s')])
                ->first();
        });

        return $this->v('logViewer.index',[
            'sevenVisits'=>$apiSevenVisits,
            'apiLogList'=>$apiLogList,
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
}
