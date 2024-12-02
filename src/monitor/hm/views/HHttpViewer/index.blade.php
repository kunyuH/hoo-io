<?php
$cdn = get_cdn().'/hm';
?>

<style>
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
                                <h5 class="card-title">近7日服务可用性</h5>
                                <h6 class="card-subtitle mb-2 text-muted">每小时更新</h6>
                                <p class="card-text mb-2">
                                    访问次数：<span id="seven-visits-count">--</span><br>
                                    平均性能：<span id="seven-visits-avg">--</span>
                                    <br>
                                    <a href="javascript:"
                                       data-title="API性能趋势明细"
                                       data-width="800px"
                                       data-height="600px"
                                       class="ky-modal"
                                       data-href={{jump_link("/hm/hhttp-log-viewer/seven-visits-item")}}
                                    >每日HHTTP性能趋势明细</a>
                                </p>
                            </div>
                        </div>
                        <div class="card ml-3">
                            <div class="card-body">
                                <a href="javascript:"
                                   data-title="近7日服务调用统计"
                                   data-width="1200px"
                                   data-height="600px"
                                   class="btn btn-outline-primary btn-sm ky-modal"
                                   data-href={{jump_link("/hm/hhttp-log-viewer/service-statistics-item")}}
                                >近7日服务调用统计(每小时更新)</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 mt-3">
        <div class="card">
            <div class="card-body table-responsive" style="height: 700px">
                <form id="form-hhttp-log-search">
                    <div class="form-group">
                        <div class="row">
                            <div class="col">
                                <input type="text" name="path" placeholder="path" class="form-control">
                            </div>
                            <div class="col">
                                <input type="text" name="options" placeholder="options" class="form-control">
                            </div>
                            <div class="col">
                                <input type="text" name="response" placeholder="response" class="form-control">
                            </div>
                            <div class="col">
                                <input type="text" name="run_path" placeholder="run path" class="form-control">
                            </div>
                            <div class="col">
                                <input type="text" name="hoo_traceid" placeholder="hoo traceid" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="form-group float-left">
                        <div class="row">
                            <div class="col">
                                <input type="text" name="start_date" placeholder="start date" value="{{$start_date}}" class="form-control">
                            </div>
                            <div class="col">
                                <input type="text" name="end_date" placeholder="end date" value="{{$end_date}}" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="float-right">
                        <a href="javascript:"
                           class="btn btn-link btn-sm"
                           onclick="resetForm('#form-hhttp-log-search')"
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
                        <th style="width: 70px;">hoo_traceid</th>
                        <th>method</th>
                        <th>path</th>
                        <th style="width: 400px">options</th>
                        <th style="width: 400px">response</th>
                        <th>run_time(ms)</th>
                        <th>url</th>
                        <th>run_path</th>
                        <th>op</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($hHttpLogList as $hHttpLog)
                        <tr>
                            <td>{{$hHttpLog->created_at}}</td>
                            <td>{{$hHttpLog->hoo_traceid}}</td>
                            <td>{{$hHttpLog->method}}</td>
                            <td>{{$hHttpLog->path}}</td>
                            <td>{{$hHttpLog->options}}</td>
                            <td>{{$hHttpLog->response}}</td>
                            <td>{{$hHttpLog->run_time}}</td>
                            <td>{{$hHttpLog->url}}</td>
                            <td>{{$hHttpLog->run_path}}</td>
                            <td>
                                <a href="javascript:"
                                   type="button"
                                   class="btn btn-link btn-sm ky-modal"
                                   data-title="详情"
                                   data-width="800px"
                                   data-height="600px"
                                   data-href={{jump_link("/hm/hhttp-log-viewer/details?id=".$hHttpLog->id)}}
                                >详情</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="float-right">
                    {{$hHttpLogList->withPath(jump_link('/hm/hhttp-log-viewer/index'))->appends(request()->query())->links('pagination::bootstrap-4')}}
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
        fillForm('#form-hhttp-log-search',params)

        //加载近7日服务可用性
        sevenVisits()
    })

    /**
     * 加载近7日服务可用性
     */
    function sevenVisits() {
        $.ajax({
            type:'get',
            url:jump_link('/hm/hhttp-log-viewer/seven-visits'),
            dataType:"json",//返回数据形式为json
            beforeSend:function(e){
                $("#seven-visits-count").html('加载中...');
                $("#seven-visits-count").html('加载中...');
            },
            success:function(result){
                $("#seven-visits-count").html(result.data.count);
                $("#seven-visits-avg").html(result.data.avg+'<span style="font-weight: 500">ms</span>');
            },
            error: function(xhr, status, error) {
                $("#seven-visits-count").html('加载失败');
                $("#seven-visits-count").html('加载失败');
            }
        });
    }

    /**
     * 搜索
     */
    $(document).on("click",".api-log-search",function(){
        var formData = $("#form-hhttp-log-search").serialize();
        var url = jump_link('/hm/hhttp-log-viewer/index?') + formData; // 拼接URL
        // 跳转
        window.location.href = url;
    })

</script>
