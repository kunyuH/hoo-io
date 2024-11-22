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
    public function list($object_id=null,$name=null,$group=null,$label=null)
    {
        return LogicalBlockModel::query()
            ->where(function (Builder $q){
                $q->whereNull('deleted_at');
            })
            ->when(!empty($object_id),function (Builder $q) use ($object_id){
                $q->where('object_id','like','%'.$object_id.'%');
            })
            ->when(!empty($name),function (Builder $q) use ($name){
                $q->where('name','like','%'.$name.'%');
            })
            ->when(!empty($group),function (Builder $q) use ($group){
                $q->where('group',$group);
            })
            ->when(!empty($label),function (Builder $q) use ($label){
                $q->where('label','like','%'.$label.'%');
            })
            ->paginate(10);
    }

    /**
     * 分组列表
     * @return void
     */
    public function groupList()
    {
        return LogicalBlockModel::query()
            ->select('group')
            ->where(function (Builder $q){
                $q->whereNull('deleted_at');
            })
            ->groupBy('group')
            ->orderBy('group')
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
     * 单个逻辑块查询【通过object_id】
     * @param $object_id
     * @return Builder|Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function firstByObjectId($object_id)
    {
        return LogicalBlockModel::query()->where('object_id',$object_id)->first();
    }

    /**
     * 复制当前逻辑块 新增一个【通过id】
     * @param $id
     * @return \Illuminate\Database\Eloquent\HigherOrderBuilderProxy|\Illuminate\Support\HigherOrderCollectionProxy|mixed|null
     */
    public function copyNew($id)
    {
        $info = $this->firstById($id)->toArray();
        if(empty($info)){
            throw new HooException('未找到逻辑块');
        };

        unset($info['id']);
        $info['name'] = $info['name'].'_副本';
        $info['object_id'] = ho_uuid();
        $info['created_at'] = date('Y-m-d H:i:s');
        $info['updated_at'] = date('Y-m-d H:i:s');

        $id = LogicalBlockModel::query()->insertGetId($info);

        // 记录日志
        LogsModel::log(__FUNCTION__.':hm_logical_block-新增',json_encode([
            'old_data'=>[],
            'new_data'=>$this->firstById($id)
        ],JSON_UNESCAPED_UNICODE));

        return $id;
    }

    /**
     * 逻辑块保存
     * @param $name
     * @param $group
     * @param $label
     * @param $logical_block
     * @param $remark
     * @param $id
     * @return void
     * @throws HooException
     */
    public function save($name,$group,$label,$logical_block,$remark='',$id=null)
    {
        if(!empty($id)){
            $old_data = $this->firstById($id);

            # 检测是否存在重复
            if(LogicalBlockModel::query()
                ->where('object_id',$old_data->object_id)
                ->where('id','<>',$id)
                ->where(function (Builder $q){
                    $q->whereNull('deleted_at');
                })->count()){throw new HooException('object_id已存在！');}

            LogicalBlockModel::query()->where('id',$id)->update([
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
            $id = LogicalBlockModel::query()->insertGetId([
                'object_id'=>ho_uuid(),
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
        return $id;
    }

    /**
     * 逻辑块保存 (根据object_id) 应用在跨系统复制中
     * @param $name
     * @param $group
     * @param $label
     * @param $logical_block
     * @param $remark
     * @param $object_id
     * @return mixed
     * @throws HooException
     */
    public function saveByobjectId($name,$group,$label,$logical_block,$remark,$object_id)
    {
        if(empty($object_id)){
            throw new HooException('object_id不能为空！');
        }

        $old_data = $this->firstByObjectId($object_id);

        # 检测是否存在重复
        if(LogicalBlockModel::query()
            ->where('object_id',$object_id)
            ->where(function (Builder $q){
                $q->whereNull('deleted_at');
            })->count()){throw new HooException("object_id：{$old_data->object_id}已存在！如需复制请先删除当前object_id再复制");}

        $id = LogicalBlockModel::query()->insertGetId([
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
            'old_data'=>$old_data,
            'new_data'=>$this->firstById($id)
        ],JSON_UNESCAPED_UNICODE));

        return $id;
    }

    /**
     * 逻辑块删除
     * @param $id
     * @return void
     * @throws HooException
     */
    public function delete($id)
    {
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
        # 字符串兼容
        if(!is_array($resData)){$resData=[$resData];}
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

            # 实例化
            $class = new \ReflectionClass($class_name);

            # 参数处理
            # 1. 上游逻辑块未传递参数 则入参置空
            # 2. 当前逻辑块不接收参数 则入参置空
            # 3. 上游逻辑块传递参数，参数为数组类型，则按顺序传递参数
            # 4. 上游逻辑块传递参数，参数为字典类型，则自动识别key，按照key传递参数，没有的自动补全默认数据
            $resData = $this->parameter($resData,$class);
            $resData = $class->newInstanceArgs()->run($resData);


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
     * 参数处理
     * 1. 上游逻辑块未传递参数，且 当前逻辑块不接收参数，则入参置空
     * 2. 上游逻辑块未传递参数，且 当前逻辑块接收参数，
     *  则入参按照当前逻辑块参数设置，没有默认值则默认null，有默认值则使用默认数据
     * 3. 当前逻辑块不接收参数 则入参置空
     * 4. 上游逻辑块传递参数，参数为字典类型，则自动识别key，按照key传递参数，没有的自动补全默认数据
     * 5. 上游逻辑块传递参数，参数为数组类型，则按顺序传递参数  无需调整
     * @param $resData
     * @param $class
     * @return void
     */
    private function parameter($resData,$class)
    {
        $data = [];
        $parameters = $class->getMethod('handle')->getParameters();
        if(empty($resData) && empty($parameters)){
            $data = [];
        }elseif (empty($resData) && !empty($parameters)){
            foreach ($parameters as $parameter){
                if(isset($resData[$parameter->name])){
                    $data[] = $resData[$parameter->name];
                }else{
                    # 判断是否有默认值 true 有默认值  如果没有默认值 则默认为null
                    if($parameter->isDefaultValueAvailable()){
                        $data[] = $parameter->getDefaultValue();
                    }else{
                        $data[] = null;
                    }
                }
            }
        }elseif (empty($parameters)){
            $data = [];
        }elseif (is_dictionary($resData)){

            foreach ($parameters as $parameter){
                if(isset($resData[$parameter->name])){
                    $data[] = $resData[$parameter->name];
                }else{
                    # 判断是否有默认值 true 有默认值  如果没有默认值 则默认为null
                    if($parameter->isDefaultValueAvailable()){
                        $data[] = $parameter->getDefaultValue();
                    }else{
                        $data[] = null;
                    }
                }
            }
        }else{
            $data = $resData;
        }

        return $data;
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
     * @param $mode 返回数据模式
     *  data 返回数据 如果存在错误 则出异常并输出错误  【默认】
     *  data and error  返回数据和异常 以数组形式
     * @return array|mixed
     */
    public function execByObjectId($object_id,$resData=[],$out_mode='data')
    {
        $block = LogicalBlockModel::query()
            ->where('object_id',$object_id)->first();
        list($resData,$error) = $this->execByCode($block->logical_block,$block->name,$resData);
        if($out_mode == 'out data'){
            if(!empty($error)){
                throw new HooException($error->getMessage(),$error->getCode());
            }
            return $resData;
        }else{
            return [$resData,$error];
        }
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
