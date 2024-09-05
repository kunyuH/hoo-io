<?php

namespace hoo\io\monitor\hm\Controllers;

use hoo\io\common\Models\LogicalBlockModel;
use hoo\io\common\Models\LogicalPipelinesArrangeModel;
use hoo\io\common\Models\LogsModel;
use hoo\io\common\Request\HmIndexRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use hoo\io\common\Support\Facade\HooSession;


class IndexController extends BaseController
{
    /**
     * 首页
     * @return string
     */
    public function index()
    {
        return $this->v('index.index');
    }

    /**
     * 执行命令
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function runCommand(HmIndexRequest $request)
    {
        if($request->isMethod('POST')) {
            $command = $request->input('value');
            $open_type = $request->input('open_type','1');
            $type = $request->input('type','5');

            $output = [];
            $return_var = 0;

            exec('php ../artisan '.$command,$output, $return_var);

            // 记录日志
            LogsModel::log(__FUNCTION__.':运行命令',json_encode([
                'command'=>$command,
                '运行结果'=>$output
            ],JSON_UNESCAPED_UNICODE));

            return $this->resSuccess([
                'open_type'=>$open_type,
                'type'=>$type,
            ],$this->execOutput($output));

        }else{
            return $this->view('main.modal-form',[
                'submitTo'=>$request->input('submitTo'),
            ]);
        }
    }

    public function runCode(HmIndexRequest $request)
    {
        if($request->isMethod('POST')) {
            $logical_block = $request->input('value');

            // 记录日志
            LogsModel::log(__FUNCTION__.':运行代码',$logical_block);

            $resData = LogicalPipelinesArrangeModel::logicalBlockExec($logical_block);

            return $resData;
        }else{
            return $this->view('main.modal-form',[
                'submitTo'=>$request->input('submitTo'),
            ]);
        }

    }
}
