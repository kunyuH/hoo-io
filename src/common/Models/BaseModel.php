<?php

namespace hoo\io\common\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * 获取完整表名
     * @return array|string|string[]
     */
    public function getTableName()
    {
        # 获取表名称
        $table = $this->getTable();
        # 获取表前缀
        $prefix = $this->getConnection()->getTablePrefix();
        return str_replace($prefix, '', $table);
    }
}
