<?php

namespace hoo\io\monitor\hm\Controllers;

use hoo\io\common\Models\LogsModel;
use hoo\io\monitor\hm\Request\LogicalBlockRequest;
use hoo\io\monitor\hm\Models\LogicalBlockModel;
use hoo\io\monitor\hm\Services\LogicalPipelinesService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Schema\Blueprint;


class LogicalBlockController extends BaseController
{
    /**
     * run LogicalBlock首页
     * @return string
     */
    public function index()
    {
        return $this->v('LogicalBlock.index');
    }

    /**
     * 获取LogicalBlock列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function list()
    {
        $list = LogicalBlockModel::query()
            ->where(function (Builder $q){
                $q->whereNull('deleted_at')
                ->orWhere('deleted_at','');
            })
            ->get();
        return $this->resSuccess($list);
    }

    /**
     * 获取LogicalBlock详情
     * @param LogicalBlockRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail(LogicalBlockRequest $request)
    {
        $id = $request->input('id');
        $item = LogicalBlockModel::query()->find($id);
        return $this->resSuccess($item);
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
        $code = $request->input('value');

        if(!empty($id)){
            if ($id == 1){
                return $this->resError([],'系统默认，不能修改！');
            }
            $old_data = LogicalBlockModel::query()->find($id);

            LogicalBlockModel::query()->where('id',$id)->update([
                'name'=>$name,
                'group'=>$group,
                'label'=>$label,
                'logical_block'=>$code,
                'updated_at'=>date('Y-m-d H:i:s')
            ]);

             // 记录日志
            LogsModel::log(__FUNCTION__.':hm_logical_block-更新',json_encode([
                'old_data'=>$old_data,
                'new_data'=>LogicalBlockModel::query()->find($id)
            ],JSON_UNESCAPED_UNICODE));
        }else{
            LogicalBlockModel::query()->create([
                'name'=>$name,
                'group'=>$group,
                'label'=>$label,
                'logical_block'=>$code,
                'created_at'=>date('Y-m-d H:i:s'),
                'updated_at'=>date('Y-m-d H:i:s')
            ]);
            // 记录日志
            LogsModel::log(__FUNCTION__.':hm_logical_block-新增',json_encode([
                'old_data'=>[],
                'new_data'=>LogicalBlockModel::query()->find($id)
            ],JSON_UNESCAPED_UNICODE));
        }
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
        if ($id == 1){
            return $this->resError([],'系统默认，不能删除！');
        }
        LogicalBlockModel::query()->where('id',$id)->update([
            'deleted_at'=>date('Y-m-d H:i:s'),
            'updated_at'=>date('Y-m-d H:i:s')
        ]);

        // 记录日志
        LogsModel::log(__FUNCTION__.':hm_logical_block-删除',json_encode([
            'id'=>$id
        ]));

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

            list($resData,) = (new LogicalPipelinesService())->logicalBlockExec($logical_block);

            return $resData;
        }else{
            return $this->view('main.modal-form',[
                'submitTo'=>$request->input('submitTo'),
            ]);
        }

    }
}
