<?php

namespace hoo\io\monitor\hm\Controllers;

use hoo\io\common\Exceptions\HooException;
use hoo\io\common\Models\LogsModel;
use hoo\io\common\Services\CryptoService;
use hoo\io\monitor\hm\Request\LogicalBlockRequest;
use hoo\io\monitor\hm\Support\Facades\LogicalBlock;


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
            'GroupList'=>LogicalBlock::groupList()
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
        $logicalBlockInfo = LogicalBlock::firstById($request->input('id'));
        if(!empty($logicalBlockInfo))
        {
            $logicalBlockInfo['logical_block'] = CryptoService::sm4Encrypt($logicalBlockInfo['logical_block']);
        }

        return $this->resSuccess($logicalBlockInfo);
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

        $logical_block = CryptoService::sm4Decrypt($logical_block);

        $id = LogicalBlock::save($name,$group,$label,$logical_block,$remark,$id);

        return $this->resSuccess(['id'=>$id]);
    }

    /**
     * 复制当前逻辑块 新增一个
     * @param LogicalBlockRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function copyNew(LogicalBlockRequest $request)
    {
        $id = LogicalBlock::copyNew($request->input('id'));

        return $this->resSuccess([]);
    }

    /**
     * 获取需要复制的逻辑块内容【加密】
     * @param LogicalBlockRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function copy(LogicalBlockRequest $request)
    {
        $logicalBlockInfo = LogicalBlock::firstById($request->input('id'));
        if(!empty($logicalBlockInfo))
        {
            $logicalBlockInfo['logical_block'] = CryptoService::sm4Encrypt($logicalBlockInfo['logical_block']);
        }
        return $this->resSuccess([
            'logical_block'=>CryptoService::sm4Encrypt(json_encode($logicalBlockInfo))
        ]);
    }

    /**
     * 粘贴的逻辑块保存
     * @param LogicalBlockRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function paste(LogicalBlockRequest $request)
    {
        if($request->isMethod('POST')) {
            $logical_block_info = $request->input('logical_block');
            $logical_block_info = CryptoService::sm4Decrypt($logical_block_info);

            $logical_block_info = json_decode($logical_block_info,true);

            $name = $logical_block_info['name'];
            $group = $logical_block_info['group'];
            $label = $logical_block_info['label'];
            $logical_block = CryptoService::sm4Decrypt($logical_block_info['logical_block']);

            $remark = $logical_block_info['remark'];
            $object_id = $logical_block_info['object_id'];

            if(empty($object_id)){
                throw new HooException('object_id不能为空');
            }

            $id = LogicalBlock::saveByobjectId($name,$group,$label,$logical_block,$remark,$object_id);

            return $this->resSuccess([
                'type'=>2,
                'id'=>$id
            ]);
        }else{
            return $this->modal('LogicalBlock.paste');
        }
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

            $logical_block = CryptoService::sm4Decrypt($logical_block);
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
