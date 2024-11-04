<?php
$cdn = get_cdn().'/hm';

function getDir($path,$html='',$data=[])
{
    $logs = glob($path.'/*', GLOB_ONLYDIR);
    foreach ($logs as $log) {
        $html .= '<div style="margin-left: 20px">';
        $html .= '<button type="button" class="btn btn-link ky-req"
        data-type="post"
        data-href="'.jump_link('/hm/log-viewer/show-log').'"
        data-params=\'{"path":"'.urlencode($log).'"}\'
        data-title="log-viewer"
        data-width="1400px"
        data-height="800px"
        >
        <i class="bi bi-folder"></i> '.basename($log).'('.count(glob($log.'/*.log')).')
        </button>';

        list($children,$html) = getDir($log,$html);
        $data[] = [
            'name' => basename($log),
            'path' => $log,
            'children' => $children
        ];

        $html .= '</div>';

    }
    return [$data,$html];
}
$log = storage_path('logs');
$html = '<div style="margin-left: 20px">';
$html .= '<button type="button" class="btn btn-link ky-req"
        data-type="post"
        data-href="'.jump_link('/hm/log-viewer/show-log').'"
        data-params=\'{"path":"'.urlencode($log).'"}\'
        data-title="log-viewer"
        data-width="1400px"
        data-height="800px"
        >
        <i class="bi bi-folder"></i> '.basename($log).'('.count(glob($log.'/*.log')).')
        </button>';
list($data,$html) = getDir($log,$html);
$html .= '</div>';
?>

<style>

    /*.table-responsive {*/
    /*    display: block;*/
    /*    width: 100%;*/
    /*    overflow-x: auto;*/
    /*    -webkit-overflow-scrolling: touch;*/
    /*}*/
    .table {
        table-layout: fixed;
        width: 100%; /* 确保表格宽度设置 */
        font-size: 14px;
    }
    .table td {
        overflow: hidden;
        text-overflow:ellipsis;
        white-space: nowrap;
    }

    .table tr th:hover, .table-responsive > .table tr td:hover {
        overflow: visible;
        white-space: normal;
    }
</style>
<div class="row">
    <div class="col-12">
        <div class="accordion" id="accordionExample">
            <div class="card">
                <div class="card-header" id="headingOne">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Group Item #1
                        </button>
                    </h2>
                </div>
                <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                    <div class="card-body d-flex">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">磁盘占用情况</h5>
                                <h6 class="card-subtitle mb-2 text-muted">每小时更新</h6>
                                <p class="card-text mb-2" id="disk-usage">
                                </p>
                            </div>
                        </div>
                        <div class="card ml-3">
                            <div class="card-body">
                                <h5 class="card-title">近7日服务可用性</h5>
                                <h6 class="card-subtitle mb-2 text-muted">每小时更新</h6>
                                <p class="card-text mb-2">
                                    访问次数：{{$sevenVisits->count}}<br>
                                    平均性能：{{intval($sevenVisits->avg)}}<span style="font-weight: 500">ms</span>
                                    <br>
                                    <a href="javascript:"
                                        data-title="API性能趋势明细"
                                        data-width="800px"
                                        data-height="600px"
                                        class="ky-modal"
                                        data-href={{jump_link("/hm/log-viewer/seven-visits-item")}}
                                    >API性能趋势明细</a>
                                </p>
                            </div>
                        </div>
                        <div class="card ml-3">
                            <div class="card-body">
                                <a href="javascript:"
                                   data-title="近7日path访问统计"
                                   data-width="1200px"
                                   data-height="600px"
                                   class="btn btn-outline-primary btn-sm ky-modal"
                                   data-href={{jump_link("/hm/log-viewer/service-statistics-item")}}
                                >近7日服务调用统计(每小时更新)</a><br>
                                <a href="javascript:"
                                   data-title="API带宽明细"
                                   data-width="1200px"
                                   data-height="600px"
                                   class="btn btn-outline-primary btn-sm ky-modal mt-2"
                                   data-href={{jump_link("/hm/log-viewer/bandwidth-statistics-item")}}
                                >API带宽明细</a>
                            </div>
                        </div>
                            <div class="card ml-3">
                                <?php echo $html?>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 mt-3">
        <div class="card">
            <div class="card-body table-responsive" style="height: 700px">
                <form id="form-api-log-search">
                    <div class="form-group">
                        <div class="row">
                            <div class="col">
                                <input type="text" name="path" placeholder="path" class="form-control">
                            </div>
                            <div class="col">
                                <input type="text" name="user_id" placeholder="user_id" class="form-control">
                            </div>
                            <div class="col">
                                <input type="text" name="hoo_traceid" placeholder="hoo_traceid" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="float-right">
                        <a href="javascript:"
                           class="btn btn-link btn-sm"
                           onclick="resetForm('#form-api-log-search')"
                        >重置</a>
                        <a href="javascript:"
                           class="btn btn-link btn-sm api-log-search"
                        >查询</a>
                    </div>
                </form>
                <table class="table table-sm table-hover table-striped">
                    <thead>
                    <tr>
                        <th style="width: 155px;">date</th>
{{--                        <th>app_name</th>--}}
                        <th style="width: 70px;">user_id</th>
                        <th style="width: 62px;">method</th>
                        <th style="width: 200px;">path</th>
                        <th>run_time(ms)</th>
                        <th>user_agent</th>
                        <th style="width: 400px;">input</th>
                        <th style="width: 400px;">output</th>
                        <th>status_code</th>
{{--                        <th>ip</th>--}}
                        <th>依赖HTTP</th>
                        <th>op</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($apiLogList as $apiLog)
                        <tr>
                            <td>{{$apiLog->created_at}}</td>
{{--                            <td>{{$apiLog->app_name}}</td>--}}
                            <td>{{$apiLog->user_id}}</td>
                            <td>{{$apiLog->method}}</td>
                            <td>{{$apiLog->path}}</td>
                            <td>{{$apiLog->run_time}}</td>
                            <td>{{$apiLog->user_agent}}</td>
                            <td>{{$apiLog->input}}</td>
                            <td>{{$apiLog->output}}</td>
                            <td>{{$apiLog->status_code}}</td>
{{--                            <td>{{$apiLog->ip}}</td>--}}
                            <td>
                                @foreach($apiLog->HttpLog as $HttpLog)
                                    {{$HttpLog->path}}<br>
                                @endforeach
                            </td>
                            <td>
                                <a href="javascript:"
                                   type="button"
                                   class="btn btn-link btn-sm ky-modal"
                                   data-title="详情"
                                   data-width="800px"
                                   data-height="600px"
                                   data-href={{jump_link("/hm/log-viewer/details?id=".$apiLog->id)}}
                                >详情</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="float-right">
                    {{$apiLogList->withPath(jump_link('/hm/log-viewer/index'))->appends(request()->query())->links('pagination::bootstrap-4')}}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    /**
     * 页面加载后运行
     */
    $(document).ready(function(){
        //获取当前页面url携带的参数
        var url = window.location.href;
        params = getUrlParams(new URL(url));

        // 遍历所有的查询参数，并填充到表单中
        fillForm('#form-api-log-search',params)

        // 加载磁盘占用情况
        loadDiskUsage()
    })

    /**
     * 加载磁盘占用情况
     */
    function loadDiskUsage() {
        $.ajax({
            type:'get',
            url:jump_link('/hm/log-viewer/disk-usage'),
            dataType:"json",//返回数据形式为json
            beforeSend:function(e){
                $("#disk-usage").html('加载中...');
            },
            success:function(result){
                $("#disk-usage").html('');
                // 遍历
                for (var key in result.data) {
                    $("#disk-usage").append(key+'：<span class="badge">'+result['data'][key][0]['Size_MB']+'</span><span style="font-weight: 500">MB</span><br>');
                }
            },
            error: function(xhr, status, error) {
                $("#disk-usage").html('加载失败！');
            }
        });
    }
    /**
     * 搜索
     */
    $(document).on("click",".api-log-search",function(){
        var formData = $("#form-api-log-search").serialize();
        var url = jump_link('/hm/log-viewer/index?') + formData; // 拼接URL
        // 跳转
        window.location.href = url;
    })
</script>
