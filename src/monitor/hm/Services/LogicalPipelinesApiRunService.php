<?php

namespace hoo\io\monitor\hm\Services;

use hoo\io\monitor\hm\Support\Facades\Logical;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use hoo\io\common\Exceptions\HooException;

class LogicalPipelinesApiRunService extends BaseService
{

    public function run(Request $request, $pipeline)
    {
        # 1.参数校验
        $validate = $pipeline->setting['validate']??'';
        $validate = json_decode($validate, true);

        $validator = Validator::make($request->all(), $validate);

        if($validate) {
            if ($validator->fails())
                throw new HooException($validator->errors()->first());
        }
        # 2.运行逻辑线
        return Logical::runById($pipeline->id,[$request]);
    }
}
