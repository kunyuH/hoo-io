<?php
    $cdn = 'https://js.tuguaishou.com/other/hm';
?>
<!doctype html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>hoo-login</title>
    <link href="{{$cdn}}/layui-v2.6.8/layui/css/layui.css" rel="stylesheet">
    <link href="{{$cdn}}/css/bootstrap.min.css" rel="stylesheet" >
</head>
<body>
<script src="{{$cdn}}/js/jquery.min.js"></script>
<script src="{{$cdn}}/js/jquery.form.min.js"></script>
<div class="container">
    <br>
    <br>
    <div class="row">
        <div class="col-12">
            <h1>hoo-login</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <form style="max-width: 400px" id="hoo-login">
                <div class="form-group">
                    <label for="name">name</label>
                    <input type="text" name="name" class="form-control">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" class="form-control" id="password">
                </div>
                <a href="javascript:"
                   data-from_id="hoo-login"
                   class="btn btn-primary formSubmit"
                   data-href={{$service_prefix."/hm/login"}}
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
    var password = document.getElementById('password');

    // 为输入框添加事件监听器
    password.addEventListener('keydown', function(event) {
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
