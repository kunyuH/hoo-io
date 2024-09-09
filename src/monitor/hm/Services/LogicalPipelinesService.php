<?php

namespace hoo\io\monitor\hm\Services;

use hoo\io\common\Exceptions\HooException;
use hoo\io\common\Models\LogsModel;
use hoo\io\monitor\hm\Models\LogicalBlockModel;
use hoo\io\monitor\hm\Models\LogicalPipelinesArrangeModel;
use hoo\io\monitor\hm\Models\LogicalPipelinesModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class LogicalPipelinesService extends BaseService
{
    /**
     * 逻辑线列表
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function list()
    {
        return LogicalPipelinesModel::query()
            ->where(function (Builder $q){
                $q->whereNull('deleted_at')
                    ->orWhere('deleted_at','');
            })
            ->get();
    }

    /**
     * 单个逻辑线查询【按照id】
     * @param $id
     * @return array|Builder|\Illuminate\Database\Eloquent\Model|object
     */
    public function firstById($id)
    {
        $data = LogicalPipelinesModel::query()->where('id',$id)->first();
        $data['setting'] = json_decode($data['setting']??'',true);
        return $data;
    }

    /**
     * 单个逻辑线查询【按照rec_subject_id】
     * @param $rec_subject_id
     * @return array|Builder|\Illuminate\Database\Eloquent\Model|object
     */
    public function firstByRecSubjectId($rec_subject_id)
    {
        $data = LogicalPipelinesModel::query()->where('rec_subject_id',$rec_subject_id)->first();
        $data['setting'] = json_decode($data['setting'],true);
        return $data;
    }

    /**
     * 逻辑线保存
     * @param $rec_subject_id
     * @param $name
     * @param $group
     * @param $label
     * @param $remark
     * @param $id
     * @return true
     * @throws HooException
     */
    public function save($rec_subject_id,$name,$group,$label='',$remark='',$setting,$id='')
    {
        if($id){
            LogicalPipelinesModel::query()->where('id',$id)->update([
                'rec_subject_id'=>$rec_subject_id,
                'name'=>$name,
                'group'=>$group,
                'label'=>$label,
                'remark'=>$remark,
                'setting'=>json_encode($setting,JSON_UNESCAPED_UNICODE),
                'updated_at'=>date('Y-m-d H:i:s')
            ]);
        }else{
            if(LogicalPipelinesModel::query()
                ->where('id','<>',$id)
                ->where('rec_subject_id',$rec_subject_id)->count()){
                throw new HooException('路由已存在！');
            }
            LogicalPipelinesModel::query()->insert([
                'rec_subject_id'=>$rec_subject_id,
                'name'=>$name,
                'group'=>$group,
                'label'=>$label,
                'remark'=>$remark,
                'setting'=>json_encode($setting,JSON_UNESCAPED_UNICODE),
                'created_at'=>date('Y-m-d H:i:s'),
                'updated_at'=>date('Y-m-d H:i:s')
            ]);
        }
        return true;
    }

    /**
     * 逻辑线删除
     * @param $id
     * @return true
     */
    public function delete($id)
    {
        if ($id == 1){
            throw new HooException('系统默认，不能删除！');
        }
        LogicalPipelinesModel::query()->where('id',$id)->update([
            'deleted_at'=>date('Y-m-d H:i:s'),
            'updated_at'=>date('Y-m-d H:i:s')
        ]);
        // 记录日志
        LogsModel::log(__FUNCTION__.':hm_logical_pipelines-删除',json_encode([
            'id'=>$id
        ]));
        return true;
    }

    /**
     * 逻辑线 编排列表
     * @param $id
     * @return array
     */
    public function arrangeList($id)
    {
        # 获取表名
        $logicalPipelinesArrangeTableName = (new LogicalPipelinesArrangeModel())->getTable();
        $logicalBlockTableName = (new LogicalBlockModel())->getTable();

        $pipeline = LogicalPipelinesArrangeModel::query()
            ->select('pipelines_arrange.*',
                'block.id as block_id',
                'block.object_id as block_object_id',
                'block.name as block_name',
                'block.group as block_group',
                'block.label as block_label',
                'block.logical_block as block_logical_block',
                'block.remark as block_remark',
            )
            # 表设置别名
            ->from($logicalPipelinesArrangeTableName.' as pipelines_arrange')
            ->leftJoin($logicalBlockTableName.' as block','pipelines_arrange.logical_block_id','=','block.id')
            ->where('pipelines_arrange.logical_pipeline_id',$id)
            ->get()->toArray();

        return $this->arrange($pipeline);
    }

    /**
     * 逻辑线 编排项添加
     * @param $pipeline_id      //逻辑线id
     * @param $arrange_id       //逻辑线项id
     * @param $logical_block_id //逻辑块id
     * @param $op               //操作类型 next：向下添加  previous：向上添加
     * @return void
     * @throws HooException
     */
    public function arrangeAddItem($pipeline_id,$arrange_id,$logical_block_id,$op)
    {
        if($op=='next'){
            /**
             * 在当前下插入一项
             * before 上一条 逻辑线项 【在此基础上增加下一条】
             * new 新增 逻辑线项
             * after 下一条 逻辑线项
             * 要使用事务
             */
            #上一条 逻辑线项id
            $before_id = $arrange_id;

            # 添加一条 逻辑线项
            $new_id = LogicalPipelinesArrangeModel::query()->insertGetId([
                'logical_pipeline_id'=>$pipeline_id,
                'logical_block_id'=>$logical_block_id,
                'next_id'=>0,
                'type'=>'common',
                'created_at'=>date('Y-m-d H:i:s'),
                'updated_at'=>date('Y-m-d H:i:s')
            ]);

            # 下一条 逻辑线项id
            $after_id = LogicalPipelinesArrangeModel::find($arrange_id)->next_id??0;

            # 修改 上一条 逻辑线项
            LogicalPipelinesArrangeModel::query()
                ->where('id',$arrange_id)
                ->update([
                    'next_id'=>$new_id,
                    'updated_at'=>date('Y-m-d H:i:s')
                ]);

            # 修改 新增的 逻辑线项
            LogicalPipelinesArrangeModel::query()
                ->where('id',$new_id)
                ->update([
                    'next_id'=>$after_id,
                    'updated_at'=>date('Y-m-d H:i:s')
                ]);
        }elseif ($op=='previous'){
            /**
             * 在当前项上插入一项
             * before 上一条 逻辑线项
             * new 新增 逻辑线项
             * after 下一条 逻辑线项   【在此基础上增加上一条】
             * 要使用事务
             * 需要做的事情
             * 1.新增一项 nex_id 值暂不设置 默认为0
             * 2.修改上一条 逻辑线项 的next_id 为新增的项id
             * 3.修改新增的项的next_id 为当前项的id
             */
            # 添加一条 逻辑线项
            $new_id = LogicalPipelinesArrangeModel::query()->insertGetId([
                'logical_pipeline_id'=>$pipeline_id,
                'logical_block_id'=>$logical_block_id,
                'next_id'=>0,
                'type'=>'common',
                'created_at'=>date('Y-m-d H:i:s'),
                'updated_at'=>date('Y-m-d H:i:s')
            ]);

            # 修改上一条 逻辑线项 的next_id 为新增的项id
            LogicalPipelinesArrangeModel::query()
                ->where('next_id',$arrange_id)
                ->update([
                    'next_id'=>$new_id,
                    'updated_at'=>date('Y-m-d H:i:s')
                ]);
            # 修改新增的项的next_id 为当前项的id
            LogicalPipelinesArrangeModel::query()
                ->where('id',$new_id)
                ->update([
                    'next_id'=>$arrange_id,
                    'updated_at'=>date('Y-m-d H:i:s')
                ]);
        }else{throw new HooException('未指定操作类型！');}
    }

    /**
     * 逻辑线 编排项删除
     * @param $arrange_id
     * @return true
     */
    public function arrangeDelete($arrange_id)
    {
        /**
         * 在当前项上插入一项
         * before 上一条 逻辑线项
         * current 删除 逻辑线项      【在此基础上增加上一条】
         * after 下一条 逻辑线项
         * 要使用事务
         * 需要做的事情
         * 1.修改上一条 逻辑线项 的next_id 为下一条 逻辑线项id
         * 2.删除当前逻辑线项
         */

        $after_id = LogicalPipelinesArrangeModel::find($arrange_id)->next_id;

        # 修改上一条 逻辑线项 的next_id 为下一条 逻辑线项id
        LogicalPipelinesArrangeModel::query()
            ->where('next_id',$arrange_id)
            ->update([
                'next_id'=>$after_id,
                'updated_at'=>date('Y-m-d H:i:s')
            ]);

        # 删除当前逻辑线项
        LogicalPipelinesArrangeModel::query()
            ->where('id',$arrange_id)
            ->delete();

        return true;
    }

    /**
     * 逻辑线 运行【通过id】
     * @param $id
     * @return void
     */
    public function runById($id,$resData = [])
    {
        $pipelines = LogicalPipelinesArrangeModel::query()
            ->where('logical_pipeline_id',$id)
            ->get()->toArray();

        $pipelines = $this->arrange($pipelines);
        return $this->exec($pipelines,$resData);
    }

    /**
     * 逻辑线 运行【通过rec_subject_id】
     * @param $id
     * @return void
     */
    public function runByRecSubjectId($rec_subject_id,$resData = [])
    {
        $pipelines = LogicalPipelinesArrangeModel::query()
            ->where('rec_subject_id',rec_subject_id)
            ->get()->toArray();

        $pipelines = $this->arrange($pipelines);
        return $this->exec($pipelines,$resData);
    }

    /**
     * 将编排结果有序排列
     * @param $pipeline
     * @return array
     */
    private function arrange($pipeline)
    {
        if(empty($pipeline)){return [];}
        # next_id 数据预处理 为null 和 为'' 都置为0
        foreach ($pipeline as $k=>$v){
            if($v['next_id'] === null){
                $pipeline[$k]['next_id'] = 0;
            }else if($v['next_id'] === ''){
                $pipeline[$k]['next_id'] = 0;
            }
        }

        # 先找到最后一个
        foreach ($pipeline as $k=>$v){
            if(empty($v['next_id'])){
                $last = $v;
                break;
            }
        }
        # 将数据 按照next_id 作为key，存入数组
        $data = [];
        foreach ($pipeline as $k=>$v){
            $data[$v['next_id']] = $v;
        }
        # 递归处理
        $out = $this->recursion($data,$last);
        # 倒排
        $out = array_reverse($out);
        return $out;
    }

    /**
     * 递归从最后一项逐步找到全部
     * @param $data
     * @param $last
     * @param $out
     * @return array|mixed
     */
    private function recursion($data,$last,$out=[],$t=0)
    {
        $t++;
        if($t>1000){
            # 防止死循环
            return $out;
        }
        $out[] = $last;
        if(isset($data[$last['id']])){
            $out = self::recursion($data,$data[$last['id']],$out,$t);
        }
        return $out;
    }

    /**
     * 逻辑线运行核心
     * @param $pipeline
     * @return array
     */
    private function exec($pipelines,$resData = [])
    {
        foreach ($pipelines as $k=>$v){
            $block = LogicalBlockModel::find($v['logical_block_id']);
            list($resData,$error) = $this->logicalBlockExecByCode($block->logical_block,$block->name,$resData);
            if(!empty($error)) {
                throw new Exception($error->getMessage(), $error->getCode());
//                if (config('app.debug', false)) {
//                    echo $error->getMessage() . PHP_EOL;
//                    echo PHP_EOL;
//                    echo $error->getTraceAsString();
//                    return [];
//                } else {
//                    throw new Exception($error->getMessage(), $error->getCode());
//                }
            }
        }
        return $resData;
    }


    public function logicalBlockExecByCode($logical_block,$name='',$resData=[])
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
                $resData = $instance->run(...$resData);
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
    public function logicalBlockExecById($id,$resData=[])
    {
        $block = LogicalBlockModel::find($id);
        list($resData,$error) = $this->logicalBlockExecByCode($block->logical_block,$block->name,$resData);
        return [$resData,$error];
    }


    /**
     * 逻辑块运行【通过对象id】
     * @param $logical_block
     * @param $name
     * @param $resData
     * @return array|mixed
     */
    public function logicalBlockExecByObjectId($object_id,$resData=[])
    {
        $block = LogicalBlockModel::query()
            ->where('object_id',$object_id)->first();
        list($resData,$error) = $this->logicalBlockExecByCode($block->logical_block,$block->name,$resData);
        return [$resData,$error];
    }

    /**
     * 记录日志
     * @param $name
     * @param $before_time
     * @param $after_time
     * @param $inputData
     * @param $resData
     * @param null|Throwable $error
     * @return void
     */
    private function log($name,$before_time,$after_time,$inputData,$resData,null|Throwable $error)
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
//                    'trace' => $error->getTrace()
                ]
            ]);
        }
    }
}
