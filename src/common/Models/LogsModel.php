<?php

namespace hoo\io\common\Models;

class LogsModel extends BaseModel
{
    protected $table = 'hm_logs';

    /**
     * 记录日志
     * @param $name
     * @param $content
     * @param $label_a
     * @param $label_b
     * @param $label_c
     * @return CodeObjectModel
     */
    public static function log($name,$content,$label_a='',$label_b='',$label_c=''){
        try {
            $log = new self();
            $log->name = $name;
            $log->content = $content;
            $log->label_a = $label_a;
            $log->label_b = $label_b;
            $log->label_c = $label_c;
            $log->save();
            return $log;
        } catch (\Exception $e) {
            return null;
        }
    }
}
