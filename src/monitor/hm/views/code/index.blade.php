<?php
$cdn = get_cdn();
?>
{{--样式css--}}
<link rel="stylesheet" href="{{$cdn}}/codemirror-5.65.17/theme/night.css">
<link rel="stylesheet" href="{{$cdn}}/codemirror-5.65.17/theme/seti.css">

{{--<link rel=stylesheet href="{{$cdn}}/codemirror-5.65.17/doc/docs.css">--}}

<link rel="stylesheet" href="{{$cdn}}/codemirror-5.65.17/lib/codemirror.css">
<script src="{{$cdn}}/codemirror-5.65.17/lib/codemirror.js"></script>
<script src="{{$cdn}}/codemirror-5.65.17/addon/edit/matchbrackets.js"></script>
<script src="{{$cdn}}/codemirror-5.65.17/mode/htmlmixed/htmlmixed.js"></script>
<script src="{{$cdn}}/codemirror-5.65.17/mode/xml/xml.js"></script>
<script src="{{$cdn}}/codemirror-5.65.17/mode/javascript/javascript.js"></script>
<script src="{{$cdn}}/codemirror-5.65.17/mode/css/css.js"></script>
<script src="{{$cdn}}/codemirror-5.65.17/mode/clike/clike.js"></script>
<script src="{{$cdn}}/codemirror-5.65.17/mode/php/php.js"></script>

<div class="row">
    <div class="col-2">
        <div class="card">
            <div class="card-header">
                code object
            </div>
            <div class="card-body">
                <a href="javascript:" type="button" class="btn btn-outline-primary btn-sm" id="hm-code-create">新增</a>
                <hr>
                <div id="code-object-list">
                </div>
            </div>
        </div>
    </div>
    <div class="col-10">
        <div class="card">
            <div class="card-body">
                <form id="hoo-run-code">
                    <input hidden name="id" id="code-object-id">
                    <div class="form-group">
                        <div class="row">
                            <div class="col">
                                <input type="text" id="code-object-name" name="name" placeholder="name" class="form-control">
                            </div>
                            <div class="col">
                                <input type="text" id="code-object-group" name="group" placeholder="group" class="form-control">
                            </div>
                            <div class="col">
                                <input type="text" id="code-object-label" name="label" placeholder="label" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="form-group CodeMirror">
                        <textarea class="form-control" id="code-object-text" name="value" rows="3"></textarea>
                    </div>
                    <div class="float-right">
                        <a href="javascript:"
                           data-from_id="hoo-run-code"
                           class="btn btn-outline-primary formRunCodeSubmit"
                           data-href={{jump_link('/hm/run-code')}}
                        >Run</a>
                        <a href="javascript:"
                           data-from_id="hoo-run-code"
                           class="btn btn-outline-success formRunCodeSave"
                           id="hm-code-object-save"
                           data-href={{jump_link('/hm/code/save')}}
                        >Create</a>
                        <a href="javascript:"
                           data-from_id="hoo-run-code"
                           class="btn btn-danger formCodeDelete"
                           data-type="POST"
                           id="hm-code-object-delete"
                           data-href={{jump_link('/hm/code/delete')}}
                        >delete</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-12 mt-3">
        <div class="card">
            <div class="card-body">
                <pre id="run-code-output" style="min-height: 500px">
                </pre>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        loadCodeObjectList();
        loadForm();
    })

    /**
     * 表单初始化
     */
    function loadForm() {
        $("#code-object-name").val('');
        $("#code-object-group").val('');
        $("#code-object-label").val('');
        $("#code-object-text").val('');
        $("#code-object-id").val('');
        // 改变按钮内容
        $("#hm-code-object-save").html('Create');
        editor.setValue("<\?php\n");
    }

    /**
     * object 列表加载
     */
    function loadCodeObjectList() {
        $.ajax({
            type:'get',
            url:'{{jump_link("/hm/code/list")}}',
            dataType:"json",//返回数据形式为json
            beforeSend:function(e){
                $('#code-object-list').html('<div class="spinner-border text-dark" style="width: 1rem;height: 1rem" role="status"><span class="sr-only">Loading...</span></div>')
            },
            success:function(e){
                if(e.code==200){
                    // 遍历 e.data
                    var html = '';
                    $.each(e.data,function(k,v){
                        html += '' +
                            '<a href="javascript:" type="button" ' +
                            'class="btn btn-outline-primary btn-sm mt-1 mr-1 code-object-item-ky-req" ' +
                            'data-type="get" data-params=\'{"id":"'+v.id+'"}\' ' +
                            'data-href={{jump_link("/hm/code/details")}}>'+v.name+'</a>';
                    })
                    $('#code-object-list').html(html);
                }else{
                    $('#code-object-list').html('【'+e.message+'】<br><span>可能是run code模块未初始化，请点击' +
                        '<a href="javascript:" ' +
                        'class="ky-req" ' +
                        'data-type="post" ' +
                        'data-confirm-ky="确认要初始化run code模块吗？" ' +
                        'data-params=\'{"value":"hm:dev runCodeInit","open_type":1,"type":0}\' ' +
                        'data-href={{jump_link("/hm/run-command")}}>run code模块初始化</a> </span>');
                }
            },
        });
    }

    /**
     * 新增
     */
    $(document).on("click","#hm-code-create",function(){

        layer.confirm('点击新增后，当前代码会丢失，请先确认是否已保存！', {
            icon:0,
            closeBtn: 0,
            btn: ['我已保存','关闭'] //按钮
        }, function(){
            layer.closeAll();
            loadForm()
        });
    })

    /**
     * CodeMirror 实例
     * @type {CodeMirror|*}
     */
    var editor = CodeMirror.fromTextArea(document.getElementById("code-object-text"), {
        lineNumbers: true,
        matchBrackets: true,
        mode: "application/x-httpd-php",
        indentUnit: 4,
        indentWithTabs: true,
        theme: 'seti',
    });

    /**
     * 代码运行
     */
    $(document).on("click",".formRunCodeSubmit",function(){
        var from_id = $(this).attr('data-from_id');
        var url = $(this).attr('data-href');
        $("#code-object-text").val(editor.getValue());
        $('#run-code-output').html('<div class="spinner-border text-dark" style="width: 1rem;height: 1rem" role="status"><span class="sr-only">Loading...</span></div>')
        $("#"+from_id).ajaxSubmit({
            type:"post",
            url:url,
            // dataType: 'json' ,
            success: function (result) {
                //如果返回的是json 则转为字符串
                if(typeof result == 'object'){
                    result = JSON.stringify(result)
                    result = result.replace(/\\n/g, "<br>").replace(/\\t/g, "&nbsp;&nbsp;&nbsp;&nbsp;")
                    $("#run-code-output").html(result);
                }else{
                    $("#run-code-output").html(result);
                }
            },
            error: function(xhr, status, error) {
                $("#run-code-output").html(xhr.responseText);
                //如果返回的是json 则转为字符串
                if(typeof xhr.responseText == 'object'){
                    responseText = JSON.stringify(xhr.responseText)
                    responseText = responseText.replace(/\\n/g, "<br>").replace(/\\t/g, "&nbsp;&nbsp;&nbsp;&nbsp;")
                    $("#run-code-output").html(responseText);
                }else{
                    $("#run-code-output").html(xhr.responseText);
                }
            }
        });
    })

    /**
     * 代码保存
     */
    $(document).on("click",".formRunCodeSave",function(){
        var from_id = $(this).attr('data-from_id');
        var url = $(this).attr('data-href');
        $("#code-object-text").val(editor.getValue());

        $("#"+from_id).ajaxSubmit({
            type:"post",
            url:url,
            dataType: 'json' ,
            beforeSend:function(e){
                layer.closeAll();
                index = layer.load(1);
            },
            success: function (result) {
                layer.close(index);
                if(result.code == 200){
                    layer.msg(result.message, {icon: 6, time: 500}, function(){
                        loadCodeObjectList()
                    });
                }else{
                    layer.alert(result.message, {icon: 5});
                }
            },
        });
    })

    /**
     * 代码删除
     */
    $(document).on("click",".formCodeDelete",function(){
        var type = $(this).attr('data-type');
        var href = $(this).attr('data-href');
        var id = $('#code-object-id').val()
        if (id == ''){
            layer.alert('请选择要删除的对象！', {icon: 5});
            return false;
        }
        $.ajax({
            type:type,
            url:href,
            data:{'id':id},
            dataType:"json",//返回数据形式为json
            beforeSend:function(e){
                layer.closeAll();
                index = layer.load(1);
            },
            success:function(e){
                layer.close(index);
                if(e.code==200){
                    layer.msg(e.message, {icon: 6, time: 500}, function(){
                        loadCodeObjectList()
                        loadForm();
                    });
                }else{
                    layer.alert(e.message,{icon:5});
                }
            },
        });
    })

    /**
     * object详情获取
     */
    $(document).on("click",".code-object-item-ky-req",function(){
        var type = $(this).attr('data-type');
        var href = $(this).attr('data-href');
        var params = $(this).attr('data-params');
        if(params){
            params = JSON.parse(params);
        }
        $.ajax({
            type:type,
            url:href,
            data:params,
            dataType:"json",//返回数据形式为json
            beforeSend:function(e){
                layer.closeAll();
                index = layer.load(1);
            },
            success:function(e){
                layer.close(index);
                if(e.code==200){
                    $("#code-object-id").val(e.data.id);
                    $("#code-object-name").val(e.data.name);
                    $("#code-object-group").val(e.data.group);
                    $("#code-object-label").val(e.data.label);
                    $("#code-object-text").val(e.data.object);
                    editor.setValue(e.data.object);
                    $("#hm-code-object-save").html('update');
                }else{
                    layer.alert(e.message,{icon:5});
                }
            },
        });
    })
</script>
