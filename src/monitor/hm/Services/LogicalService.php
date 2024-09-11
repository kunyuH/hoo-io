<?php

namespace hoo\io\monitor\hm\Services;

class LogicalService
{
    public function run($impot=[])
    {
        // TODO 执行前钩子
        
        $output = $this->handle(...$impot);
        // TODO 执行后钩子

        return $output;
    }
}
