<?php

namespace hoo\io\monitor\hm\Controllers;

use hoo\io\common\Exceptions\HooException;
use hoo\io\monitor\hm\Models\LogicalBlockModel;
use hoo\io\monitor\hm\Models\LogicalPipelinesArrangeModel;
use hoo\io\monitor\hm\Models\LogicalPipelinesModel;
use hoo\io\common\Models\LogsModel;
use hoo\io\monitor\hm\Request\LogicalPipelinesRequest;
use hoo\io\monitor\hm\Services\LogicalPipelinesService;
use Illuminate\Database\Eloquent\Builder;


class LogicalPipelinesController extends BaseController
{

    /**
     * @var LogicalPipelinesService
     */
    public $pipelines;

    public function __construct()
    {
        $this->pipelines = new LogicalPipelinesService();
    }
    /**
     * logical pipelines首页
     * @return string
     */
    public function index()
    {
        return $this->v('logicalPipelines.index',[
            'logicalPipelines'=>$this->pipelines->list()
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
            $this->pipelines->save(
                $request->input('rec_subject_id'),
                $request->input('name'),
                $request->input('group'),
                $request->input('label'),
                $request->input('remark'),
                $id);
            return $this->resSuccess(['type'=>2]);
        }else{
            $info = [];
            if($id){
                $info = LogicalPipelinesModel::query()->where('id',$id)->first();
            }

            $info['setting'] = json_decode($info['setting']??"{}",true);
            
//            dd($info['setting']);
//            $info['setting'] = [
//                'method'=>'POST',
//                'middleware'=>'',
//                //参数校验策略
//                'validate'=>[]
//            ];

            return $this->modal('logicalPipelines.save',$info);
        }
    }

    /**
     * 删除
     * @param LogicalPipelinesRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(LogicalPipelinesRequest $request)
    {
        $id = $request->input('id');
        $this->pipelines->delete($id);
        return $this->resSuccess();
    }

    /**
     * 运行逻辑线
     * @return \Illuminate\Http\JsonResponse
     */
    public function run(LogicalPipelinesRequest $request)
    {
        $id = $request->input('id');
        return $this->pipelines->run($id);
    }

    /**
     * 编排列表
     * @param LogicalPipelinesRequest $request
     * @return void
     */
    public function arrange(LogicalPipelinesRequest $request)
    {
        return $this->view('logicalPipelines.arrange',[
            'pipelineData'=>$this->pipelines->pipelineArrangeList($request->input('id'))
        ]);
    }

    /**
     * 添加编排项
     * @param LogicalPipelinesRequest $request
     * @return \Illuminate\Http\JsonResponse|string
     * @throws HooException
     */
    public function addArrangeItem(LogicalPipelinesRequest $request)
    {
        $pipeline_id = $request->input('pipeline_id');
        $arrange_id = $request->input('arrange_id');
        $op = $request->input('op');
        if($request->isMethod('POST')) {
            $logical_block_id = $request->input('logical_block_id');

            $this->pipelines->addPipelineArrangeItem($pipeline_id,$arrange_id,$logical_block_id,$op);
            return $this->resSuccess([
                'type'=>6,
                'redirect_uri'=>jump_link("/hm/logical-pipelines/index?pipeline_id={$pipeline_id}"),
            ]);
        }else{
            return $this->modal('logicalPipelines.addArrangeItem',[
                'logical_blocks'=>LogicalBlockModel::query()
                    ->where(function (Builder $q){
                        $q->whereNull('deleted_at')
                            ->orWhere('deleted_at','');
                    })->get(),
                'pipeline_id'=>$pipeline_id,
                'arrange_id'=>$arrange_id,
                'op'=>$op,
            ]);
        }
    }

    /**
     * 删除逻辑线编排项
     * @param LogicalPipelinesRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteArrange(LogicalPipelinesRequest $request)
    {
        $pipeline_id = $request->input('pipeline_id');
        $arrange_id = $request->input('arrange_id');
        $this->pipelines->deleteArrange($arrange_id);
        return $this->resSuccess([
            'type'=>6,
            'redirect_uri'=>jump_link("/hm/logical-pipelines/index?pipeline_id={$pipeline_id}"),
        ]);
    }
}
