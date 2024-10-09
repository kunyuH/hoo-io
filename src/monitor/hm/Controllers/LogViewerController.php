<?php
namespace hoo\io\monitor\hm\Controllers;

use hoo\io\common\Support\Facade\HooSession;
use Illuminate\Http\Request;

class LogViewerController extends BaseController
{
    public function index()
    {
        return $this->v('logViewer.index');
    }

    public function showLog(Request $request)
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
