<?php
namespace hoo\io\monitor\hm\Controllers;

use hoo\io\common\Models\ApiLogModel;
use hoo\io\common\Support\Facade\HooSession;
use hoo\io\monitor\hm\Request\LogViewerRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogViewerController extends BaseController
{
    public function index(LogViewerRequest $request)
    {
        $path = $request->input('path');
        $user_id = $request->input('user_id');

        $apiLogList = ApiLogModel::query()
            ->with(['HttpLog'])
            ->when(!empty($path),function (Builder $q) use ($path){
                $q->where('path','=',$path);
            })
            ->when(!empty($user_id),function (Builder $q) use ($user_id){
                $q->where('user_id','=',$user_id);
            })
            ->orderBy('id','desc')
            ->paginate(20);

        # 获取7天前时间
        $sevenDaysAgo = date('Y-m-d H:i:s',strtotime('-7 days'));

        # 获取近7日path访问统计
        $apiLogStatisticsList = ApiLogModel::query()
            ->select('path',
                DB::raw('count(*) as count'),
                DB::raw('avg(run_time) as avg'))
//            ->when(!empty($path),function (Builder $q) use ($path){
//                $q->where('path','=',$path);
//            })
//            ->when(!empty($user_id),function (Builder $q) use ($user_id){
//                $q->where('user_id','=',$user_id);
//            })
            ->whereBetween('created_at',[$sevenDaysAgo,date('Y-m-d H:i:s')])
            ->groupBy('path')
            ->orderBy('count','desc')
            ->get();

        # 获取近日天访问量
        $sevenVisits = ApiLogModel::query()
            ->whereBetween('created_at',[$sevenDaysAgo,date('Y-m-d H:i:s')])
            ->count();
        # 获取近7日平均性能
        $sevenAveragePer = ApiLogModel::query()
            ->whereBetween('created_at',[$sevenDaysAgo,date('Y-m-d H:i:s')])
            ->avg('run_time');

        return $this->v('logViewer.index',[
            'sevenVisits'=>$sevenVisits,
            'sevenAveragePer'=>$sevenAveragePer,
            'apiLogStatisticsList'=>$apiLogStatisticsList,
            'apiLogList'=>$apiLogList,
        ]);
    }

    public function details(LogViewerRequest $request)
    {
        $id = $request->input('id');

        $apiLog = ApiLogModel::query()
            ->with(['HttpLog'])
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
