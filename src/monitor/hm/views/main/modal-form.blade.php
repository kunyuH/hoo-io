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
<div class="container">
    <div class="row">
        <div class="col-12">
            <form id="hoo-modal-form">
                <div class="form-group">
                    <br>
                    <br>
                    <textarea name="value" class="form-control" id="hoo-modal-form-value"></textarea>
                </div>
                <div class="float-right">
                    <a href="javascript:"
                       data-from_id="hoo-modal-form"
                       class="btn btn-primary formSubmit"
                       data-href={{jump_link($submitTo)}}
                    >Submit</a>
                    <a href="javascript:"
                       class="btn btn-danger  modalClose"
                    >Close</a>
                </div>

            </form>
        </div>
    </div>
</div>
<script src="<?php echo $cdn?>/js/bootstrap.bundle.min.js"></script>

<script src="<?php echo $cdn?>/layui-v2.6.8/layui/layui.js"></script>
<script src="<?php echo $cdn?>/js/overall.js"></script>
<script src="<?php echo $cdn?>/js/main.js"></script>
<script>
    var jump_link = function (url) {
        return '{{jump_link("")}}' + url;
    }
</script>
</body>
</html>
