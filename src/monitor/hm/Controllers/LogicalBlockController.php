<?php

namespace hoo\io\monitor\hm\Controllers;

use hoo\io\common\Exceptions\HooException;
use hoo\io\common\Models\LogsModel;
use hoo\io\monitor\hm\Support\Facades\Logical;
use hoo\io\monitor\hm\Request\LogicalBlockRequest;
use hoo\io\monitor\hm\Models\LogicalBlockModel;
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
        $object_id = $request->input('object_id');
        $name = $request->input('name');
        $group = $request->input('group');
        $label = $request->input('label');
        $logical_block = $request->input('logical_block');

        if(!empty($id)){
            if ($id == 1){
                return $this->resError([],'系统默认，不能修改！');
            }
            $old_data = LogicalBlockModel::query()->find($id);

            # 检测是否存在重复
            if(LogicalBlockModel::query()
                ->where('object_id',$object_id)
                ->where('id','<>',$id)
                ->where(function (Builder $q){
                    $q->whereNull('deleted_at')
                        ->orWhere('deleted_at','');
                })->count()){throw new HooException('object_id已存在！');}

            LogicalBlockModel::query()->where('id',$id)->update([
                'object_id'=>$object_id,
                'name'=>$name,
                'group'=>$group,
                'label'=>$label,
                'logical_block'=>$logical_block,
                'updated_at'=>date('Y-m-d H:i:s')
            ]);

             // 记录日志
            LogsModel::log(__FUNCTION__.':hm_logical_block-更新',json_encode([
                'old_data'=>$old_data,
                'new_data'=>LogicalBlockModel::query()->find($id)
            ],JSON_UNESCAPED_UNICODE));
        }else{
            # 检测是否存在重复
            if(LogicalBlockModel::query()
                ->where('object_id',$object_id)
                ->where(function (Builder $q){
                    $q->whereNull('deleted_at')
                        ->orWhere('deleted_at','');
                })->count()){throw new HooException('object_id已存在！');}

            LogicalBlockModel::query()->create([
                'object_id'=>$object_id,
                'name'=>$name,
                'group'=>$group,
                'label'=>$label,
                'logical_block'=>$logical_block,
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

            list($resData,) = Logical::logicalBlockExecByCode($logical_block);

            return $resData;
        }else{
            return $this->view('main.modal-form',[
                'submitTo'=>$request->input('submitTo'),
            ]);
        }

    }
}
