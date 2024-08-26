<?php

namespace hoo\io\monitor\hm\Controllers;

use hoo\io\common\Models\CodeObjectModel;
use hoo\io\common\Models\LogsModel;
use hoo\io\common\Request\HmCodeRequest;


class CodeController extends BaseController
{
    /**
     * run code首页
     * @return string
     */
    public function index()
    {
        return $this->v('code.index');
    }

    /**
     * 获取code列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function list()
    {
        $list = CodeObjectModel::query()->get();
        return $this->resSuccess($list);
    }

    /**
     * 获取code详情
     * @param HmCodeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function details(HmCodeRequest $request)
    {
        $id = $request->input('id');
        $item = CodeObjectModel::query()->find($id);
        return $this->resSuccess($item);
    }

    /**
     * 保存code
     * @param HmCodeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(HmCodeRequest $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $label = $request->input('label');
        $code = $request->input('value');

        if(!empty($id)){
            $old_data = CodeObjectModel::query()->find($id);

             CodeObjectModel::query()->where('id',$id)->update([
                'name'=>$name,
                'label'=>$label,
                'object'=>$code,
                'updated_at'=>date('Y-m-d H:i:s')
            ]);

             // 记录日志
            LogsModel::log(__FUNCTION__.':hm_code_object表-更新',json_encode([
                'old_data'=>$old_data,
                'new_data'=>CodeObjectModel::query()->find($id)
            ],JSON_UNESCAPED_UNICODE));
        }else{
            CodeObjectModel::query()->create([
                'name'=>$name,
                'label'=>$label,
                'object'=>$code,
                'created_at'=>date('Y-m-d H:i:s'),
                'updated_at'=>date('Y-m-d H:i:s')
            ]);
            // 记录日志
            LogsModel::log(__FUNCTION__.':hm_code_object表-新增',json_encode([
                'old_data'=>[],
                'new_data'=>CodeObjectModel::query()->find($id)
            ],JSON_UNESCAPED_UNICODE));
        }
        return $this->resSuccess([
            'open_type'=>1,
            'type'=>5,
        ]);
    }

    public function delete(HmCodeRequest $request)
    {

    }

}