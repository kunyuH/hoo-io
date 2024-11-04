<?php
$cdn = get_cdn().'/hm';

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
    <div class="col-12 mt-3">
        <div class="card">
            <div class="card-body table-responsive">
                <form id="form-api-log-search">
                    <div class="form-group">
                        <div class="row">
                            <div class="col">
                                <input type="text" name="path" placeholder="path" value="{{$path}}" class="form-control">
                            </div>
                            <div class="col">
                                <input type="text" name="startDate" placeholder="startDate" value="{{$startDate}}" class="form-control">
                            </div>
                            <div class="col">
                                <input type="text" name="endDate" placeholder="endDate" value="{{$endDate}}" class="form-control">
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
                <table class="table table-sm table-hover">
                    <thead>
                    <tr>
                        <th style="width: 25px">#</th>
                        <th style="width: 400px;">path</th>
                        <th>调用次数</th>
                        <th>平均入参数据包(b)</th>
                        <th>平均出参数据包(b)</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($apiLogList as $k => $apiLog)
                        <tr>
                            <td>{{$k+1}}</td>
                            <td>{{$apiLog->path}}</td>
                            <td>{{$apiLog->count}}</td>
                            <td>{{$apiLog->in_byte}}</td>
                            <td>{{$apiLog->out_byte}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="float-right">
                    {{$apiLogList->withPath(jump_link('/hm/log-viewer/bandwidth-statistics-item'))->appends(request()->query())->links('pagination::bootstrap-4')}}
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
    })
    /**
     * 搜索
     */
    $(document).on("click",".api-log-search",function(){
        var formData = $("#form-api-log-search").serialize();
        var url = jump_link('/hm/log-viewer/bandwidth-statistics-item?') + formData; // 拼接URL
        // 跳转
        window.location.href = url;
    })
</script>
