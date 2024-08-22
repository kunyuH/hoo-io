<?php

namespace hoo\io\monitor\hm\Controllers;

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
            $output = [];
            $return_var = 0;

            exec('php ../artisan '.$command,$output, $return_var);

            return $this->resSuccess([
                'open_type'=>1,
                'type'=>5,
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
            $code = $request->input('value');

            # 将code保存到缓存
            $key = HooSession::getId();
            Cache::put($key, $code, 1000);

            $command = 'hm:runCode '.$key;
            
            $output = [];
            $return_var = 0;

            exec('php ../artisan '.$command,$output, $return_var);

            return $this->resSuccess([
                'open_type'=>1,
                'type'=>5,
            ],$this->execOutput($output));

        }else{
            return $this->view('main.modal-form',[
                'submitTo'=>$request->input('submitTo'),
            ]);
        }

    }
}
