<?php

namespace hoo\io\monitor\hm\Support\Facades;

use hoo\io\monitor\hm\Services\LogicalBlockService;
use Illuminate\Support\Facades\Facade;

/**
 * 可编程逻辑块模块
 *
 * **********************************基础信息更新*****************************************
 * 逻辑块列表
 * @method static list($object_id,$name,$group,$label)
 * @var LogicalBlockService::list()
 *
 *  逻辑块 所有分组
 * @method static groupList($object_id, $name, $group, $label)
 * @var LogicalBlockService::groupList()
 *
 * 单个逻辑块查询【通过id】
 * @method static firstById($id)
 * @var LogicalBlockService::firstById()
 *
 * 复制当前逻辑块 新增一个【通过id】
 * @method static copyNew($id)
 * @var LogicalBlockService::copyNew()
 * 
 * 逻辑块保存
 * @method static save($name,$group,$label,$logical_block,$remark='',$id=null)
 * @var LogicalBlockService::save()
 *
 * 逻辑块保存 (根据object_id) 应用在跨系统复制中
 * @method static saveByobjectId($name, $group, $label, $logical_block, $remark , $object_id )
 * @var LogicalBlockService::saveByobjectId()
 *
 * 逻辑块删除
 * @method static delete($id)
 * @var LogicalBlockService::delete()
 *
 * **************************运行**********************************
 * 逻辑块运行【通过id】
 * @method static execById($id, $resData = [], $out_mode='data')
 * @var LogicalBlockService::execById()
 *
 * 逻辑块运行【通过对象id】
 * @method static execByObjectId($object_id, $resData = [], $out_mode='data')
 * @var LogicalBlockService::execByObjectId()
 *
 * 逻辑块运行【通过源代码直接运行】
 * @method static execByCode($logical_block, $name = '', $resData = [])
 * @var LogicalBlockService::execByCode()
 *
 * see LogicalBlockService
 */
class LogicalBlock extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return LogicalBlockService::class;
    }
}
