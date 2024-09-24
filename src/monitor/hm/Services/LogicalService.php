<?php

namespace hoo\io\monitor\hm\Services;

class LogicalService
{
    public function run($impot=[])
    {
        // TODO 执行前钩子

        # true 传递了参数
        if(!empty($impot)){
            $output = $this->handle(...$impot);
        }else{
            $output = $this->handle();
        }

        // TODO 执行后钩子

        return $output;
    }
}
