<?php
namespace hoo\io\monitor\hm\Controllers;

use hoo\io\common\Exceptions\HooException;
use hoo\io\common\Services\LogSearch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

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

    /**
     * 日志搜索
     * @param Request $request
     * @return string
     * @throws \hoo\io\common\Exceptions\HooException
     */
    public function search(Request $request)
    {
        $path = $request->input('path');
        $keyword = $request->input('keyword');
        $limit = $request->input('limit',10);
        $page = $request->input('page',1);
        if(empty($path)){
            $path = base_path().$this->log_path;
        }else{
            $path = base_path().$this->log_path.$path;
        }
        $fileExtension = 'log'; // 可选.指定日志文件后缀

        $offset = ($page-1)*$limit;

        $list = (new LogSearch($path,$fileExtension))
            ->where($keyword)
            ->limit($limit,$offset)
            ->get();
        $count = (new LogSearch($path,$fileExtension))
            ->where($keyword)
            ->limit($limit,$offset)
            ->count();

        foreach ($list as &$item){
            $item = htmlspecialchars($item);
        }

        return $this->resSuccess([
            'list'=>$list,
            'count'=>$count,
            'page'=>$page,
        ]);
    }

    /**
     * 删除日志
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws HooException
     */
    public function del(Request $request)
    {
        $path = $request->input('path');
        if(empty($path)){throw new HooException('请选择要删除的文件');}
        $path = base_path().$this->log_path.$path;
        # 判断是否文件夹
        if(is_dir($path)){
            // 使用Laravel的File门面获取文件夹内所有文件
            $files = File::files($path);

            // 遍历并打印出所有文件的路径
            foreach ($files as $file) {
                unlink($file->getPathname());
            }
            # 删除文件夹
            rmdir($path);
        }else if(file_exists($path)){
            unlink($path);
        }


        return $this->resSuccess('删除成功');

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
