<?php

namespace hoo\io\monitor\hm\Controllers;

use hoo\io\common\Models\CodeObjectModel;
use hoo\io\common\Models\LogsModel;
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
            $open_type = $request->input('open_type','1');
            $type = $request->input('type','5');

            $output = [];
            $return_var = 0;

            exec('php ../artisan '.$command,$output, $return_var);

            // 记录日志
            LogsModel::log(__FUNCTION__.':运行命令',json_encode([
                'command'=>$command,
                '运行结果'=>$output
            ],JSON_UNESCAPED_UNICODE));

            return $this->resSuccess([
                'open_type'=>$open_type,
                'type'=>$type,
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

            // 记录日志
            LogsModel::log(__FUNCTION__.':运行代码',$code);

            try{
                // 将变量内容写入临时文件
                $tmpfname = tempnam(sys_get_temp_dir(), 'phpinclude');
                file_put_contents($tmpfname, $code);

                include $tmpfname;
                unlink($tmpfname);

                $class = new \ReflectionClass('Foo');

                $instance = $class->newInstanceArgs();

                $instance->run();
            }catch (\Error|\Exception|Throwable|ReflectionException|\UnexpectedValueException $e){
                if(file_exists($tmpfname)){
                    unlink($tmpfname);
                }
                echo $e->getMessage().PHP_EOL;
                echo PHP_EOL;
                echo $e->getTraceAsString();
            }
            return '';
        }else{
            return $this->view('main.modal-form',[
                'submitTo'=>$request->input('submitTo'),
            ]);
        }

    }
}
