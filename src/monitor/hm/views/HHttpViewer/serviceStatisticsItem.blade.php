<?php

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
<table class="table table-striped">
    <thead>
    <tr>
        <th>path</th>
        <th>count</th>
        <th>平均耗时(ms)</th>
    </tr>
    </thead>
    <tbody>
    @foreach($hHttpLogStatisticsList as $hHttpLogStatistics)
        <tr>
            <td>{{$hHttpLogStatistics->path}}</td>
            <td>{{$hHttpLogStatistics->count}}</td>
            <td>{{intval($hHttpLogStatistics->avg)}}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<script>
</script>
