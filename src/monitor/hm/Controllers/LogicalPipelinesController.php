<?php

namespace hoo\io\monitor\hm\Controllers;

use hoo\io\common\Models\LogicalBlockModel;
use hoo\io\common\Models\LogicalPipelinesArrangeModel;
use hoo\io\common\Models\LogicalPipelinesModel;
use hoo\io\common\Models\LogsModel;
use hoo\io\common\Request\HmCodeRequest;
use hoo\io\common\Request\LogicalPipelinesRequest;
use Illuminate\Database\Eloquent\Builder;


class LogicalPipelinesController extends BaseController
{
    /**
     * logical pipelines首页
     * @return string
     */
    public function index()
    {
        $logicalPipelines = LogicalPipelinesModel::query()
            ->where(function (Builder $q){
                $q->whereNull('deleted_at')
                    ->orWhere('deleted_at','');
            })
            ->get();
        return $this->v('logicalPipelines.index',[
            'logicalPipelines'=>$logicalPipelines
        ]);
    }

    /**
     * 保存logical pipelines
     * @param LogicalPipelinesRequest $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function save(LogicalPipelinesRequest $request)
    {
        $id = $request->input('id');
        if($request->isMethod('POST')) {
            if ($id == 1){
                return $this->resError([],'系统默认，不能修改！');
            }
            if($id){
                LogicalPipelinesModel::query()->where('id',$id)->update([
                    'route'=>$request->input('route'),
                    'name'=>$request->input('name'),
                    'group'=>$request->input('group'),
                    'label'=>$request->input('label'),
                    'updated_at'=>date('Y-m-d H:i:s')
                ]);
            }else{
                if(LogicalPipelinesModel::query()
                    ->where('id','<>',$id)
                    ->where('route',$request->input('route'))->count()){
                    return $this->resError([],'路由已存在！');
                }
                LogicalPipelinesModel::query()->insert([
                    'route'=>$request->input('route'),
                    'name'=>$request->input('name'),
                    'group'=>$request->input('group'),
                    'label'=>$request->input('label'),
                    'created_at'=>date('Y-m-d H:i:s'),
                    'updated_at'=>date('Y-m-d H:i:s')
                ]);
            }
            return $this->resSuccess(['type'=>2]);
        }else{
            $info = [];
            if($id){
                $info = LogicalPipelinesModel::query()->where('id',$id)->first();
            }
            return $this->modal('logicalPipelines.save',$info);
        }
    }

    /**
     * 删除
     * @param HmCodeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(LogicalPipelinesRequest $request)
    {
        $id = $request->input('id');
        if ($id == 1){
            return $this->resError([],'系统默认，不能删除！');
        }
        LogicalPipelinesModel::query()->where('id',$id)->update([
            'deleted_at'=>date('Y-m-d H:i:s'),
            'updated_at'=>date('Y-m-d H:i:s')
        ]);
        // 记录日志
        LogsModel::log(__FUNCTION__.':hm_logical_pipelines-删除',json_encode([
            'id'=>$id
        ]));
        return $this->resSuccess();
    }

    /**
     * 运行逻辑线
     * @return \Illuminate\Http\JsonResponse
     */
    public function run(LogicalPipelinesRequest $request)
    {
        $id = $request->input('id');
        return LogicalPipelinesArrangeModel::run($id);
    }

    /**
     * 编排
     * @param LogicalPipelinesRequest $request
     * @return void
     */
    public function arrange(LogicalPipelinesRequest $request)
    {
        # 获取表名
        $logicalPipelinesArrangeTableName = (new LogicalPipelinesArrangeModel())->getTable();
        $logicalBlockTableName = (new LogicalBlockModel())->getTable();

        $id = $request->input('id');
        $pipeline = LogicalPipelinesArrangeModel::query()
            ->select('pipelines_arrange.*',
                'block.id as block_id',
                'block.name as block_name',
                'block.group as block_group',
                'block.label as block_label',
                'block.logical_block as block_logical_block',
                'block.remark as block_remark',
            )
            # 表设置别名
            ->from($logicalPipelinesArrangeTableName.' as pipelines_arrange')
            ->leftJoin($logicalBlockTableName.' as block','pipelines_arrange.logical_block_id','=','block.id')
            ->where('pipelines_arrange.logical_pipeline_id',$id)
            ->get()->toArray();

        $pipelineData = LogicalPipelinesArrangeModel::arrange($pipeline);

        return $this->view('logicalPipelines.arrange',[
            'pipelineData'=>$pipelineData
        ]);
    }

    public function addNext(LogicalPipelinesRequest $request)
    {
        $id = $request->input('id');
        $arrange_id = $request->input('arrange_id');
        if($request->isMethod('POST')) {
            $logical_block_id = $request->input('logical_block_id');

            /**
             * before 上一条 逻辑线项
             * new 新增 逻辑线项
             * after 下一条 逻辑线项
             * 要使用事务
             */

            #上一条 逻辑线项id
            $before_id = $arrange_id;

            # 添加一条 逻辑线项
            $new_id = LogicalPipelinesArrangeModel::query()->insertGetId([
                'logical_pipeline_id'=>$id,
                'logical_block_id'=>$logical_block_id,
                'next_id'=>0,
                'type'=>'common',
                'created_at'=>date('Y-m-d H:i:s'),
                'updated_at'=>date('Y-m-d H:i:s')
            ]);

            # 下一条 逻辑线项id
            $after_id = LogicalPipelinesArrangeModel::find($arrange_id)->next_id;

            # 修改 上一条 逻辑线项
            LogicalPipelinesArrangeModel::query()
                ->where('id',$arrange_id)
                ->update([
                    'next_id'=>$new_id,
                    'updated_at'=>date('Y-m-d H:i:s')
                ]);

            # 修改 新增的 逻辑线项
            LogicalPipelinesArrangeModel::query()
                ->where('id',$new_id)
                ->update([
                    'next_id'=>$after_id,
                    'updated_at'=>date('Y-m-d H:i:s')
                ]);

            return $this->resSuccess();

        }else{
            return $this->modal('logicalPipelines.addNext',[
                'logical_blocks'=>LogicalBlockModel::query()
                    ->where(function (Builder $q){
                        $q->whereNull('deleted_at')
                            ->orWhere('deleted_at','');
                    })->get(),
                'id'=>$id,
                'arrange_id'=>$arrange_id,
            ]);
        }
    }
}
