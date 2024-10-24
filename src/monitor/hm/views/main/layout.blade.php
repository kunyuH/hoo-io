<?php
$cdn = get_cdn().'/hm';
?>
<!doctype html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>hoo-hm</title>
    <link href="<?php echo $cdn?>/layui-v2.6.8/layui/css/layui.css" rel="stylesheet">
    <link href="<?php echo $cdn?>/css/bootstrap.min.css" rel="stylesheet" >
    <link rel="stylesheet" href="<?php echo $cdn?>/icons-1.11.3/font/bootstrap-icons.css">
</head>
<body>
<div class="container-fluid" style="margin-top: 15px">
    <ul class="layui-nav">
        <li class="layui-nav-item"><a href={{jump_link("/hm/index")}}>首页</a></li>
        <li class="layui-nav-item"><a href={{jump_link("/hm/logical-block/index")}}>logical block</a></li>
        <li class="layui-nav-item"><a href={{jump_link("/hm/logical-pipelines/index")}}>logical pipelines</a></li>
        <li class="layui-nav-item"><a href="javascript:"
                                      data-title="clockwork"
                                      data-width="1200px"
                                      data-height="600px"
                                      class="ky-modal"
                                      data-href={{jump_link("/clockwork/app")}}
            >clockwork</a></li>
        <li class="layui-nav-item"><a href="javascript:"
                                      data-title="log-viewer"
                                      data-width="1200px"
                                      data-height="600px"
                                      class="ky-modal"
                                      data-href={{jump_link("/log-viewer")}}
            >log-viewer</a></li>
        <li class="layui-nav-item"><a href={{jump_link("/hm/log-viewer/index")}}>log-viewer(Arcanedev)</a></li>
        <li class="layui-nav-item"><a href={{jump_link("/hm/hhttp-log-viewer/index")}}>依赖服务监控(HHTTP)</a></li>
        <li class="layui-nav-item"><a href={{jump_link("/hm/sql-log-viewer/index")}}>依赖服务监控(DATABASE)</a></li>
        <li class="layui-nav-item">
            <a href="javascript:">其他</a>
            <dl class="layui-nav-child">
                <dd><a target="_blank" href={{jump_link("/clockwork/app")}}>clockwork</a></dd>
                <dd><a target="_blank" href={{jump_link("/log-viewer")}}>log-viewer</a></dd>
                <dd><a target="_blank" href={{jump_link("/hm/log-viewer/index")}}>log-viewer(Arcanedev)</a></dd>
            </dl>
        </li>
        <li class="layui-nav-item"><a href="javascript:"
                                      data-type="post"
                                      data-confirm-ky="确认要退出吗？"
                                      class="ky-req"
                                      data-href={{jump_link("/hm/logout")}}
            >退出</a></li>
    </ul>
    <script src="<?php echo $cdn?>/js/jquery.min.js"></script>
    <script src="<?php echo $cdn?>/js/jquery.form.min.js"></script>
    <script src="<?php echo $cdn?>/js/bootstrap.bundle.min.js"></script>

    <script src="<?php echo $cdn?>/layui-v2.6.8/layui/layui.js"></script>
    <script src="<?php echo $cdn?>/js/overall.js"></script>
    <script src="<?php echo $cdn?>/js/base64js.min.js"></script>
    <script src="<?php echo $cdn?>/js/sm4js.js"></script>
    <script src="<?php echo $cdn?>/js/main.js"></script>

    <style>
        .star::after{
            content:" *";
            color:red
        }
    </style>
    <script>
        var jump_link = function (url) {
            return '{{jump_link("")}}' + url;
        }
    </script>

    <?php echo $content ?>
</div>


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
