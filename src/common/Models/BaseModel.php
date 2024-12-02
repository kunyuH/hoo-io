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

    protected $connection = 'hoo-mysql';

    /**
     * 获取完整表名
     * @return array|string|string[]
     */
    public function getTableName()
    {
        return  $this->getConnection()->getTablePrefix().$this->getTable();
    }
}
