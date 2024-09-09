<?php

namespace hoo\io\monitor\hm\Support\Facades;

use Illuminate\Support\Facades\Facade;
use hoo\io\monitor\hm\Services\LogicalPipelinesService;

/**
 * 逻辑线模块
 *
 * 逻辑线列表
 * @method static list()
 * @var LogicalPipelinesService::list()
 *
 * 单个逻辑线查询【按照id】
 * @method static firstById($id)
 * @var LogicalPipelinesService::firstById()
 *
 * 单个逻辑线查询【按照rec_subject_id】
 * @method static firstByRecSubjectId($rec_subject_id)
 * @var LogicalPipelinesService::firstByRecSubjectId()
 *
 * 逻辑线保存
 * @method static save($rec_subject_id,$name,$group,$label='',$remark='',$setting,$id='')
 * @var LogicalPipelinesService::save()
 *
 * 逻辑线删除
 * @method static delete($id)
 * @var LogicalPipelinesService::delete()
 *
 *
 * 逻辑线 编排列表
 * @method static arrangeList($id)
 * @var LogicalPipelinesService::arrangeList()
 *
 * 逻辑线 编排项添加
 * @method static arrangeAddItem($pipeline_id,$arrange_id,$logical_block_id,$op)
 * @var LogicalPipelinesService::arrangeAddItem()
 *
 * 逻辑线 编排项删除
 * @method static arrangeDelete($pipeline_id, $arrange_id, $logical_block_id, $op)
 * @var LogicalPipelinesService::arrangeDelete()
 *
 *
 * 逻辑线 运行【通过id】
 * @method static runById($id,$resData = [])
 * @var LogicalPipelinesService::runById()
 *
 * 逻辑线 运行【通过rec_subject_id】
 * @method static runByRecSubjectId($rec_subject_id, $resData = [])
 * @var LogicalPipelinesService::runByRecSubjectId()
 *
 * 逻辑块运行【通过id】
 * @method static logicalBlockExecById($id, $resData = [])
 * @var LogicalPipelinesService::logicalBlockExecById()
 *
 * 逻辑块运行【通过对象id】
 * @method static logicalBlockExecByObjectId($object_id, $resData = [])
 * @var LogicalPipelinesService::logicalBlockExecById()
 *
 * 逻辑块运行【通过源代码直接运行】
 * @method static logicalBlockExecByCode($logical_block, $name = '', $resData = [])
 * @var LogicalPipelinesService::logicalBlockExecByCode()
 *
 * @see LogicalPipelinesService
 */
class Logical extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return LogicalPipelinesService::class;
    }
}
