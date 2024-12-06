<?php
namespace hoo\io\monitor\hm\Controllers;

use hoo\io\common\Services\LogSearch;
use Illuminate\Http\Request;

class HooLogController extends BaseController
{
    public function index(Request $request)
    {
        return $this->v('HooLog.index');
    }

    public $log_path = "/storage/logs";

    /**
     * 获取目录内文件树
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPathTree(Request $request)
    {
        $path = $request->input('path',base_path().$this->log_path);
        $directory = get_directory_tree($path);

        $file_tree = $this->to_layui_data($directory);

        return $this->resSuccess($file_tree);
    }

    public function search(Request $request)
    {
        $path = $request->input('path');
        $keyword = $request->input('keyword');
        $limit = $request->input('limit',10);
        $offset = $request->input('offset',0);
        if(empty($path)){
            $path = base_path().$this->log_path;
        }
        if(empty($limit)){
            $limit = 10;
        }
        if(empty($offset)){
            $offset = 0;
        }

        $fileExtension = 'log'; // 可选.指定日志文件后缀

        $path = base_path().$this->log_path.$path;

        $list = (new LogSearch($path))->search($keyword, $limit, $offset, $fileExtension);

//        dd($list);
        return $this->view('HooLog.search',[
            'list'=>$list,
            'path'=>$path,
            'keyword'=>$keyword,
            'limit'=>$limit,
            'offset'=>$offset,
        ]);
    }

    private function to_layui_data($directory,$path='')
    {
        $data = [];
        # 文件 或 文件夹 当前所在目录
        $x_path = $path;
        foreach ($directory as $key => $item) {
            if (is_array($item)){
                # 子文件目录
                $c_x_path = "{$x_path}/{$key}";
                $data[] = [
                    'title' => $key,
                    'file_path' => $c_x_path,
                    'children' => $this->to_layui_data($item,$c_x_path),
                ];
            } else {
                $data[] = [
                    'title' => $item,
                    'file_path' => "{$x_path}/{$item}",
                ];
            }
        }
        return $data;
    }
}
