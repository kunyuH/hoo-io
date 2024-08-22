<?php

$cdn = get_cdn();

?>
<!doctype html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>hoo-login</title>
    <link href="{{$cdn}}/layui-v2.6.8/layui/css/layui.css" rel="stylesheet">
    <link href="{{$cdn}}/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<script src="{{$cdn}}/js/jquery.min.js"></script>
<script src="{{$cdn}}/js/jquery.form.min.js"></script>
<div class="container">
    <div class="row">
        <div class="col-12">
            <form id="hoo-modal-form">
                <div class="form-group">
                    <br>
                    <br>
                    <textarea name="value" class="form-control" id="hoo-modal-form-value"></textarea>
                </div>
                <a href="javascript:"
                   data-from_id="hoo-modal-form"
                   class="btn btn-primary formSubmit"
                   data-href={{jump_link($submitTo)}}
                >Submit</a>
            </form>
        </div>
    </div>
</div>
<script src="{{$cdn}}/js/bootstrap.bundle.min.js"></script>

<script src="{{$cdn}}/layui-v2.6.8/layui/layui.js"></script>
<script src="{{$cdn}}/js/overall.js"></script>

<script>
    // 获取输入框元素
    var value = document.getElementById('hoo-modal-form-value');

    // 为输入框添加事件监听器
    value.addEventListener('keydown', function (event) {
        // 检查按下的键是否是Enter键
        if (event.key === 'Enter') {
            // 调用处理函数
            handleEnterPress();
        }
    });

    // 定义处理Enter按下时的函数
    function handleEnterPress() {
        $(".formSubmit").click();
    }
</script>
</body>
</html>
