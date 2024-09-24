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
 * 单个逻辑块查询【通过id】
 * @method static firstById($id)
 * @var LogicalBlockService::firstById()
 *
 * 逻辑块保存
 * @method static save($object_id,$name,$group,$label,$logical_block,$remark='',$id=null)
 * @var LogicalBlockService::save()
 *
 * 逻辑块删除
 * @method static delete($id)
 * @var LogicalBlockService::delete()
 *
 * **************************运行**********************************
 * 逻辑块运行【通过id】
 * @method static execById($id, $resData = [])
 * @var LogicalBlockService::execById()
 *
 * 逻辑块运行【通过对象id】
 * @method static execByObjectId($object_id, $resData = [])
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
