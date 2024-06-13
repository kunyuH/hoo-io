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
            $name_or_id_crd_no_sql = " and (b.RSDNT_NM like :name_or_id_crd_no or b.id_crd_no like :name_or_id_crd_no) ";
            $where['name_or_id_crd_no'] = "%{$name_or_id_crd_no}%";
        }

        $area_sql = '';
        if (!empty($area)){
            if (strpos($area, ',') !== false){
                $area = explode(',',$area);
            }else{$area = [$area];}

            $area_sql = " and (b.CURR_ADDR_TWN_CD in ( :area ) or b.CURR_ADDR_VLG_CD in ( :area)) ";
            $where['area'] = $area;
        }

        # 医疗机构搜索
        $hospital_sql = '';
        if (!empty($hospital)) {
            if (strpos($hospital, ',') !== false){
                $hospital = explode(',',$hospital);
            }else{$hospital = [$hospital];}

            $hospital_sql = " and (case
                when b.IS_DOC_SIGN = '1' then substr(DOC_SIGN_ORG_ID,1,8)
                else b.HLTH_RCD_MNG_ORG_ID
              end)  in (:hospital)";
            $where['hospital'] = $hospital;
        }

        # 注册时间搜索
        $register_start_time_sql = '';
        $register_end_time_sql = '';
        if (!empty($register_start_time)){
            $register_start_time_sql = " and a.created_at >= :register_start_time ";
            $where['register_start_time'] = $register_start_time. ' 00:00:00';
        }
        if (!empty($register_end_time)){
            $register_end_time_sql = " and a.created_at <= :register_end_time ";
            $where['register_end_time'] = $register_end_time. ' 23:59:59';
        }

        $sql = "select
b.RSDNT_NM as nm  -- 姓名
,b.gdr_nm as gdr  -- 性别
,b.CURR_AGE_YEAR as age  -- 年龄
,b.slf_tel_no as tel_no  -- 电话
,b.id_crd_no as id_crd_no  -- 身份证号
,CONCAT(b.CURR_ADDR_TWN_NM,b.CURR_ADDR_VLG_NM) as area  -- 完整所属区域
,b.DOC_SIGN_ORG_ID as DOC_SIGN_ORG_ID  -- 签约机构ID
,b.HLTH_RCD_MNG_ORG_ID as HLTH_RCD_MNG_ORG_ID  -- 建档机构ID
,(case
    when b.IS_DOC_SIGN = '1' then replace(b.DOC_SIGN_ORG_NM,'玉环市','')
    else b.HLTH_RCD_MNG_ORG_NM
  end) as org_nm  -- 医疗机构
,case when b.IS_DOC_SIGN = '1' then b.DOC_SIGN_STFF_NM else '' end as sgn_dct_nm -- 签约医生
,a.created_at rgst_dt -- 注册时间
,a1.`上月新增` as newly_added_last_month  -- 上月新增健康币
,a1.`本月新增` as new_additions_this_month  -- 本月新增健康币
,a.accrue_point  -- 年度累计健康币
,b.CURR_ADDR_TWN_CD as twn_cd  -- 所属区域
,b.CURR_ADDR_TWN_NM as twn_nm  -- 所属区域
,b.CURR_ADDR_VLG_CD as vlg_cd  -- 所属区域
,b.CURR_ADDR_VLG_NM as vlg_nm  -- 所属区域
FROM `lek_health`.`pa_members_health_coin` a
inner join dwd.mdl_psn_rcd b on a.id_crd_chk_no = b.user_id
left join(
  SELECT
  id_crd_chk_no
  ,sum(case when a.created_at >=date_sub(date_sub(date_sub(curdate(),interval 1 day ),interval dayofmonth(date_sub(curdate(),interval 1 day )) - 1 day),interval 1 month)
                and a.created_at < date_sub(date_sub(curdate(),interval 1 day ),interval dayofmonth(date_sub(curdate(),interval 1 day )) - 1 day)  then real_point
            else 0 end) as '上月新增'
  ,sum(case when a.created_at >=date_sub(date_sub(curdate(),interval 1 day ),interval dayofmonth(date_sub(curdate(),interval 1 day )) - 1 day)
                and a.created_at < date_add(date_sub(date_sub(curdate(),interval 1 day ),interval dayofmonth(date_sub(curdate(),interval 1 day )) - 1 day),interval 1 month) then real_point
            else 0 end) as '本月新增'
FROM lek_health.`pa_members_health_coin_logs` a
group by id_crd_chk_no
) a1 on a.id_crd_chk_no = a1.id_crd_chk_no
where
    1 = 1
     {$name_or_id_crd_no_sql}
     {$area_sql}
     {$hospital_sql}
     {$register_start_time_sql}
     {$register_end_time_sql}
";
        # 增加搜索条件 注册时间只显示本年
        $sql .= " AND a.created_at >= DATE_SUB(CURDATE(),INTERVAL dayofyear(now())-1 DAY)";

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
