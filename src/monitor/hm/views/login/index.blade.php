
<!doctype html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>hoo-login</title>
    <link href="/hm-r/layui-v2.6.8/layui/css/layui.css" rel="stylesheet">
    <link href="/hm-r/css/bootstrap.min.css" rel="stylesheet" >
</head>
<body>
<script src="/hm-r/js/jquery.min.js"></script>
<script src="/hm-r/js/jquery.form.min.js"></script>
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
                    <input type="password" name="password" class="form-control">
                </div>
                <a href="javascript:"
                   data-from_id="hoo-login"
                   class="btn btn-primary formSubmit"
                   data-href="/hm/login"
                >Submit</a>
            </form>
        </div>
    </div>
</div>
<script src="/hm-r/js/bootstrap.bundle.min.js"></script>

<script src="/hm-r/layui-v2.6.8/layui/layui.js"></script>
<script src="/hm-r/js/overall.js"></script>

<script>
    //Demo
    // console.log($('#hoo-login')).ajaxSubmit();
</script>
</body>
</html>
