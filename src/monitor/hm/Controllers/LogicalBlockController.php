<?php

namespace hoo\io\monitor\hm\Controllers;

use hoo\io\common\Exceptions\HooException;
use hoo\io\common\Models\LogsModel;
use hoo\io\monitor\hm\Support\Facades\Logical;
use hoo\io\monitor\hm\Request\LogicalBlockRequest;
use hoo\io\monitor\hm\Models\LogicalBlockModel;
use hoo\io\monitor\hm\Support\Facades\LogicalBlock;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Schema\Blueprint;


class LogicalBlockController extends BaseController
{
    /**
     * run LogicalBlock首页
     * @return string
     */
    public function index(LogicalBlockRequest $request)
    {
        $object_id = $request->input('object_id');
        $name = $request->input('name');
        $group = $request->input('group');
        $label = $request->input('label');
        return $this->v('LogicalBlock.index',[
            'LogicalBlocks'=>LogicalBlock::list($object_id,$name,$group,$label),
        ]);
    }

    /**
     * 获取LogicalBlock列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function list()
    {
        return $this->resSuccess(LogicalBlock::list());
    }

    /**
     * 获取LogicalBlock详情
     * @param LogicalBlockRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail(LogicalBlockRequest $request)
    {
        return $this->resSuccess(LogicalBlock::firstById($request->input('id')));
    }

    /**
     * 保存LogicalBlock
     * @param LogicalBlockRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(LogicalBlockRequest $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $group = $request->input('group');
        $label = $request->input('label');
        $remark = $request->input('remark');
        $logical_block = $request->input('logical_block');
        LogicalBlock::save($name,$group,$label,$logical_block,$remark,$id);
        return $this->resSuccess();
    }

    /**
     * 删除LogicalBlock
     * @param LogicalBlockRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(LogicalBlockRequest $request)
    {
        $id = $request->input('id');

        LogicalBlock::delete($id);
        return $this->resSuccess();
    }

    /**
     * 运行代码块
     * @param LogicalBlockRequest $request
     * @return string
     */
    public function run(LogicalBlockRequest $request)
    {
        if($request->isMethod('POST')) {
            $logical_block = $request->input('logical_block');

            // 记录日志
            LogsModel::log(__FUNCTION__.':运行代码',$logical_block);

            list($resData,) = LogicalBlock::execByCode($logical_block);

            return $resData;
        }else{
            return $this->view('main.modal-form',[
                'submitTo'=>$request->input('submitTo'),
            ]);
        }

    }
}
