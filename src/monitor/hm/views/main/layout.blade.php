<?php

?>
<!doctype html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>hoo-hm</title>
    <link href="/hm-r/layui-v2.6.8/layui/css/layui.css" rel="stylesheet">
    <link href="/hm-r/css/bootstrap.min.css" rel="stylesheet" >
</head>
<body>
<div class="container-fluid" style="margin-top: 15px">
    <ul class="layui-nav">
        <li class="layui-nav-item"><a href="/hm/index">首页</a></li>
        <li class="layui-nav-item"><a target="_blank" href="/clockwork/app">clockwork</a></li>
        <li class="layui-nav-item"><a target="_blank" href="/log-viewer">log-viewer</a></li>
        <li class="layui-nav-item"><a href="javascript:"
                                      data-title="clockwork"
                                      data-width="1200px"
                                      data-height="600px"
                                      class="ky-modal"
                                      data-href="/clockwork/app"
            >clockwork</a></li>
        <li class="layui-nav-item"><a href="javascript:"
                                      data-title="clockwork"
                                      data-width="1200px"
                                      data-height="600px"
                                      class="ky-modal"
                                      data-href="/log-viewer"
            >log-viewer</a></li>
        <li class="layui-nav-item"><a href="/zentao-v2/build/list">版本管理</a></li>
        <li class="layui-nav-item"><a href="/zentao-v2/build/pipeline">流水线</a></li>
        <li class="layui-nav-item">
            <a href="javascript:"
               data-title="测试"
               data-width="800px"
               data-height="500px"
               class="ky-modal"
               data-href="/ding/list"
            >测试</a>
        </li>
        <li class="layui-nav-item">
            <a href="javascript:">甘特图排期</a>
            <dl class="layui-nav-child">
                <dd><a href="javascript:"
                       data-type="get"
                       data-confirm-ky="确认要全量同步吗？"
                       class="ky-req"
                       data-href="/zentao-v2/build/synchronous"
                    >全量同步</a></dd>
                <dd><a href="javascript:"
                       data-type="get"
                       data-confirm-ky="确认要差异同步吗？"
                       class="ky-req"
                       data-href="/zentao-v2/build/diff-synchronous"
                    >差异同步</a></dd>
                <dd><a target="_blank" href="https://nvboeslwvm.feishu.cn/docx/TQY4ddyydoX3sHxDiiUcuH10n8f">查看甘特图排期</a></dd>
            </dl>
        </li>
        <li class="layui-nav-item">
            <a href="javascript:">其他</a>
            <dl class="layui-nav-child">
                <dd><a href="/zentao-v2/build/check">版本核对</a></dd>
                <dd><a href="/zentao-v2/build/daily">日报</a></dd>
                <dd><a href="/zentao-v2/build/check_story">需求核对</a></dd>
                <dd><a href="/zentao-v2/build/f_group">组明细</a></dd>
                <dd><a href="/zentao-v2/build/member">组统计</a></dd>
                <dd><a target="_blank" href="https://zentao-v2.group-ds.com/index.php?m=project&f=browse&projectID=4&status=all">禅道【玉环】</a></dd>
                <dd><a target="_blank" href="https://zentao-v2.group-ds.com/index.php?m=execution&f=task&executionID=194#app=project">禅道【后端技术部】</a></dd>
                <dd><a target="_blank" href="https://demo.group-ds.com/gp-cd-tool/#/regional-version">禅道中台</a></dd>
            </dl>
        </li>

        <li class="layui-nav-item"><a href="javascript:"
                                      data-type="post"
                                      data-confirm-ky="确认要退出吗？"
                                      class="ky-req"
                                      data-href="/hm/logout"
            >退出</a></li>
    </ul>
    <script src="/hm-r/js/jquery.min.js"></script>
    <?php echo $content ?>
</div>
<script src="/hm-r/js/bootstrap.bundle.min.js"></script>

<script src="/hm-r/layui-v2.6.8/layui/layui.js"></script>
<script src="/hm-r/js/overall.js"></script>

<script>
    /**
     * 根据路由自动选中当前导航
     */
    $('.layui-nav').children('li').each(function () {
        if($(this).children('a').attr('href') === window.location.pathname){
            $(this).addClass('layui-this')
        }
        $(this).children('dl').children('dd').each(function () {
            if($(this).children('a').attr('href') === window.location.pathname){
                $(this).addClass('layui-this')
                $(this).parent('dl').parent('li').children('a').addClass('layui-this')
            }
        })
    })
</script>
</body>
</html>
