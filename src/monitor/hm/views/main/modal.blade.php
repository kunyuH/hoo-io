<?php

$cdn = get_cdn().'/hm';

?>
<!doctype html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>hoo-modal</title>
    <link href="<?php echo $cdn?>/layui-v2.6.8/layui/css/layui.css" rel="stylesheet">
    <link href="<?php echo $cdn?>/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $cdn?>/icons-1.11.3/font/bootstrap-icons.css">
</head>
<body>
<script src="<?php echo $cdn?>/js/jquery.min.js"></script>
<script src="<?php echo $cdn?>/js/jquery.form.min.js"></script>

<script src="<?php echo $cdn?>/js/bootstrap.bundle.min.js"></script>

<script src="<?php echo $cdn?>/layui-v2.6.8/layui/layui.js"></script>
<script src="<?php echo $cdn?>/js/overall.js"></script>
<script>
    $(function () {
        $('.modalClose').click(function () {
            parent.layer.closeAll();
        });
    })
</script>
<style>
    .star::after{
        content:" *";
        color:red
    }
</style>
<div class="container">
    <?php echo $content ?>
</div>

</body>
</html>
