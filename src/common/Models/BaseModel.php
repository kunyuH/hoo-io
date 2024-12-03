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

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        if(config('hoo-io.HOO_DATABASE_DEFAULT')){
            $this->connection = config('hoo-io.HOO_DATABASE_DEFAULT'); // 从配置文件动态获取连接
        }
    }

    /**
     * 获取完整表名
     * @return array|string|string[]
     */
    public function getTableName()
    {
        return  $this->getConnection()->getTablePrefix().$this->getTable();
    }
}
