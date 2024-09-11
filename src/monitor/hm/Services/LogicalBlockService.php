<?php

namespace hoo\io\monitor\hm\Services;


use hoo\io\common\Exceptions\HooException;
use hoo\io\common\Models\LogsModel;
use hoo\io\monitor\hm\Models\LogicalBlockModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class LogicalBlockService extends BaseService
{
    /**
     * 逻辑块列表
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function list()
    {
        return LogicalBlockModel::query()
            ->where(function (Builder $q){
                $q->whereNull('deleted_at')
                    ->orWhere('deleted_at','');
            })
            ->get();
    }

    /**
     * 单个逻辑块查询【通过id】
     * @param $id
     * @return Builder|Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function firstById($id)
    {
        return LogicalBlockModel::query()->find($id);
    }

    /**
     * 逻辑块保存
     * @param $object_id
     * @param $name
     * @param $group
     * @param $label
     * @param $logical_block
     * @param $remark
     * @param $id
     * @return void
     * @throws HooException
     */
    public function save($object_id,$name,$group,$label,$logical_block,$remark='',$id=null)
    {
        if(!empty($id)){
            if ($id == 1){
                throw new HooException('系统默认，不能修改！');
            }

            $old_data = $this->firstById($id);

            # 检测是否存在重复
            if(LogicalBlockModel::query()
                ->where('object_id',$object_id)
                ->where('id','<>',$id)
                ->where(function (Builder $q){
                    $q->whereNull('deleted_at')
                        ->orWhere('deleted_at','');
                })->count()){throw new HooException('object_id已存在！');}

            LogicalBlockModel::query()->where('id',$id)->update([
                'object_id'=>$object_id,
                'name'=>$name,
                'group'=>$group,
                'label'=>$label,
                'remark'=>$remark,
                'logical_block'=>$logical_block,
                'updated_at'=>date('Y-m-d H:i:s')
            ]);

            // 记录日志
            LogsModel::log(__FUNCTION__.':hm_logical_block-更新',json_encode([
                'old_data'=>$old_data,
                'new_data'=>$this->firstById($id)
            ],JSON_UNESCAPED_UNICODE));
        }else{
            # 检测是否存在重复
            if(LogicalBlockModel::query()
                ->where('object_id',$object_id)
                ->where(function (Builder $q){
                    $q->whereNull('deleted_at')
                        ->orWhere('deleted_at','');
                })->count()){throw new HooException('object_id已存在！');}

            LogicalBlockModel::query()->create([
                'object_id'=>$object_id,
                'name'=>$name,
                'group'=>$group,
                'label'=>$label,
                'remark'=>$remark,
                'logical_block'=>$logical_block,
                'created_at'=>date('Y-m-d H:i:s'),
                'updated_at'=>date('Y-m-d H:i:s')
            ]);
            // 记录日志
            LogsModel::log(__FUNCTION__.':hm_logical_block-新增',json_encode([
                'old_data'=>[],
                'new_data'=>$this->firstById($id)
            ],JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * 逻辑块删除
     * @param $id
     * @return void
     * @throws HooException
     */
    public function delete($id)
    {
        if ($id == 1){
            throw new HooException('系统默认，不能修改！');
        }

        LogicalBlockModel::query()->where('id',$id)->update([
            'deleted_at'=>date('Y-m-d H:i:s'),
            'updated_at'=>date('Y-m-d H:i:s')
        ]);

        // 记录日志
        LogsModel::log(__FUNCTION__.':hm_logical_block-删除',json_encode([
            'id'=>$id
        ]));
    }

    /**
     * 逻辑块运行【通过源码直接运行】
     * @param $logical_block
     * @param $name
     * @param $resData
     * @return array
     * @throws \ReflectionException
     */
    public function execByCode($logical_block,$name='',$resData=[])
    {
        $before_time = microtime(true);
        $inputData = $resData;

        $error = null;
        try{
            # 加载时应用的类名
            $class_name = 'Foo_'.md5(time().Uuid::uuid1()->toString());
            # 字符串替换
            $logical_block = str_replace('Foo',$class_name,$logical_block);

            // 将变量内容写入临时文件
            $tmpfname = tempnam(sys_get_temp_dir(), 'logical-block:');
            file_put_contents($tmpfname, $logical_block);

            include $tmpfname;
            unlink($tmpfname);

            $class = new \ReflectionClass($class_name);
            $instance = $class->newInstanceArgs();

            if(!empty($resData)){
                $resData = $instance->run($resData);
            }else{
                $resData = $instance->run();
            }

        }catch (Throwable $e){
            if(file_exists($tmpfname)){
                unlink($tmpfname);
            }
            $error = $e;
        }
        $after_time = microtime(true);

        $this->log($name,$before_time,$after_time,$inputData,$resData,$error);

        return [$resData,$error];
    }

    /**
     * 逻辑块运行【通过id】
     * @param $logical_block
     * @param $name
     * @param $resData
     * @return array|mixed
     */
    public function execById($id,$resData=[])
    {
        $block = LogicalBlockModel::find($id);
        list($resData,$error) = $this->execByCode($block->logical_block,$block->name,$resData);
        return [$resData,$error];
    }

    /**
     * 逻辑块运行【通过对象id】
     * @param $logical_block
     * @param $name
     * @param $resData
     * @return array|mixed
     */
    public function execByObjectId($object_id,$resData=[])
    {
        $block = LogicalBlockModel::query()
            ->where('object_id',$object_id)->first();
        list($resData,$error) = $this->execByCode($block->logical_block,$block->name,$resData);
        return [$resData,$error];
    }

    /**
     * 记录逻辑块运行日志
     * @param $name
     * @param $before_time
     * @param $after_time
     * @param $inputData
     * @param $resData
     * @param null|Throwable $error
     * @return void
     */
    private function log($name,$before_time,$after_time,$inputData,$resData,$error)
    {
        # 记录日志 格式化记录数组
        if(empty($error)){
            Log::channel('debug')->log('info', "【logical block】{$name}", [
                '耗时' => round($after_time - $before_time, 3) * 1000 . 'ms',
                'input' => $inputData,
                'out' => $resData,
            ]);
        }else{
            Log::channel('debug')->log('error', "【logical block】{$name}", [
                '耗时' => round($after_time - $before_time, 3) * 1000 . 'ms',
                'input' => $inputData,
                'out' => $resData,
                'error' => [
                    'code' => $error->getCode(),
                    'message' => $error->getMessage(),
                    'file' => $error->getFile(),
                    'line' => $error->getLine(),
                    'trace' => $error->getTrace()
                ]
            ]);
        }
    }
}
