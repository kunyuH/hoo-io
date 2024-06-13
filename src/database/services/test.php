<?php

use Illuminate\Support\Facades\DB;

class Test
{
    /**
     * 获取健康银行人群
     * @param $name_or_id_crd_no
     * @param $area
     * @param $hospital
     * @param $register_start_time
     * @param $register_end_time
     * @return mixed
     */
    public function healthCrowd($name_or_id_crd_no, $area, $hospital, $register_start_time, $register_end_time)
    {
        $where = [];
        $name_or_id_crd_no_sql = '';
        # 患者搜索 患者姓名；身份证号
        if(!empty($name_or_id_crd_no)){
            $name_or_id_crd_no_sql = " and name_or_id_crd_no like :name_or_id_crd_no) ";
            $where['name_or_id_crd_no'] = "%{$name_or_id_crd_no}%";
        }

        $area_sql = '';
        if (!empty($area)){
            if (strpos($area, ',') !== false){
                $area = explode(',',$area);
            }else{$area = [$area];}

            $area_sql = " and area in ( :area)) ";
            $where['area'] = $area;
        }

        # 医疗机构搜索
        $hospital_sql = '';
        if (!empty($hospital)) {
            if (strpos($hospital, ',') !== false){
                $hospital = explode(',',$hospital);
            }else{$hospital = [$hospital];}

            $hospital_sql = " and hospital  in (:hospital)";
            $where['hospital'] = $hospital;
        }

        # 注册时间搜索
        $register_start_time_sql = '';
        $register_end_time_sql = '';
        if (!empty($register_start_time)){
            $register_start_time_sql = " and created_at >= :register_start_time ";
            $where['register_start_time'] = $register_start_time. ' 00:00:00';
        }
        if (!empty($register_end_time)){
            $register_end_time_sql = " and created_at <= :register_end_time ";
            $where['register_end_time'] = $register_end_time. ' 23:59:59';
        }

        $sql = "select
        * from test
where
    1 = 1
     {$name_or_id_crd_no_sql}
     {$area_sql}
     {$hospital_sql}
     {$register_start_time_sql}
     {$register_end_time_sql}
";
        /**
         * @var hoo\io\database\services\BuilderMacroSql::getSqlQuery()
         */
        return DB::connection('mysql_adb')->query()
            ->getSqlQuery()
            ->bindings($sql,$where);
    }
}

$name_or_id_crd_no = '杨灿荣';
$area = '331084101820,331069101001';
$hospital = '05584953,4763280442';
$register_start_time = '2024-04-10';
$register_end_time = '2024-05-10';

$test = new Test();
$data = $test->healthCrowd($name_or_id_crd_no, $area, $hospital, $register_start_time, $register_end_time)
    ->paginate();
