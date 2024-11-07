<?php

namespace hoo\io\monitor\hm\Controllers;

use hoo\io\common\Exceptions\HooException;
use hoo\io\common\Services\CryptoService;
use hoo\io\monitor\hm\Support\Facades\Logical;
use hoo\io\monitor\hm\Request\LogicalPipelinesRequest;
use hoo\io\monitor\hm\Support\Facades\LogicalBlock;


class LogicalPipelinesController extends BaseController
{
    /**
     * logical pipelines首页
     * @return string
     */
    public function index(LogicalPipelinesRequest $request)
    {
        $rec_subject_id = $request->input('rec_subject_id');
        $name = $request->input('name');
        $group = $request->input('group');
        $label = $request->input('label');
        return $this->v('logicalPipelines.index',[
            'logicalPipelines'=>Logical::list($rec_subject_id,$name,$group,$label)
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
            Logical::save(
                $request->input('rec_subject_id'),
                $request->input('name'),
                $request->input('group'),
                $request->input('label'),
                $request->input('remark'),
                $request->input('setting'),
                $id);
            return $this->resSuccess(['type'=>2]);
        }else{
            $info = [];
            if($id){
                $info = Logical::firstById($id);
            }
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
        Logical::delete($id);
        return $this->resSuccess();
    }

    /**
     * 运行逻辑线
     * @return \Illuminate\Http\JsonResponse
     */
    public function run(LogicalPipelinesRequest $request)
    {
        $id = $request->input('id');
        return Logical::runById($id);
    }

    /**
     * 编排列表
     * @param LogicalPipelinesRequest $request
     * @return void
     */
    public function arrange(LogicalPipelinesRequest $request)
    {
        return $this->view('logicalPipelines.arrange',[
            'arranges'=>Logical::arrangeList($request->input('id')),
            'pipeline'=>Logical::firstById($request->input('id')),
        ]);
    }

    /**
     * 编辑编排
     * @param LogicalPipelinesRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function arrangeEdit(LogicalPipelinesRequest $request)
    {
        $arrange_id = $request->input('arrange_id');
        $logical_block = $request->input('logical_block');
        $name = $request->input('name');
        // 解密
        $logical_block = CryptoService::sm4Decrypt($logical_block);

        Logical::arrangeEdit($arrange_id,$logical_block,$name);
        return $this->resSuccess();
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
            $type = $request->input('type');
            $logical_block_id = $request->input('logical_block_id');
            $logical_block = $request->input('logical_block');
            $name = $request->input('name');

            // 解密
            $logical_block = CryptoService::sm4Decrypt($logical_block);

            Logical::arrangeAddItem($pipeline_id,$arrange_id,$type,$logical_block_id,$logical_block,$name,$op);

            return $this->resSuccess([
                'redirect_uri'=>jump_link("/hm/logical-pipelines/index?pipeline_id={$pipeline_id}"),
            ]);
        }else{
            return $this->modal('logicalPipelines.addArrangeItem',[
                'logical_blocks'=>LogicalBlock::list(),
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
    public function arrangeDelete(LogicalPipelinesRequest $request)
    {
        $pipeline_id = $request->input('pipeline_id');
        $arrange_id = $request->input('arrange_id');
        Logical::arrangeDelete($arrange_id);
        return $this->resSuccess([
            'type'=>6,
            'redirect_uri'=>jump_link("/hm/logical-pipelines/index?pipeline_id={$pipeline_id}"),
        ]);
    }
}
