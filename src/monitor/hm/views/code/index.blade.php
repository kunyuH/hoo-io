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
                           class="btn btn-primary formRunCodeSubmit"
                           data-href={{jump_link('/hm/run-code')}}
                        >Run</a>
                        <a href="javascript:"
                           data-from_id="hoo-run-code"
                           class="btn btn-success formSubmit"
                           data-href={{jump_link('/hm/code/save')}}
                        >Save</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
    <div class="col-2">
        <div class="card">
            <div class="card-header">
                code object
            </div>
            <div class="card-body">
                <a href="javascript:" type="button" class="btn btn-outline-primary btn-sm hm-code-create">新增</a>
                <hr>
                <div id="code-object-list">
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 mt-3">
        <div class="card">
            <div class="card-body">
                <pre id="run-code-output">
                </pre>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        /**
         * object 列表加载
         */
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
                            'class="btn btn-primary btn-sm code-object-item-ky-req" ' +
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
    })

    /**
     * 新增
     */
    $(document).on("click","#hm-code-create",function(){

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
            dataType: 'json' ,
            success: function (result) {
                if(result.code == 200){
                    $("#run-code-output").html(result.message);
                }else{
                    layer.alert(result.message, {icon: 5});
                    $("#run-code-output").html('');
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
                    $("#code-object-label").val(e.data.label);
                    $("#code-object-text").val(e.data.object);
                    editor.setValue(e.data.object);
                    // editorx.setValue(e.data.object);
                }else{
                    layer.alert(e.message,{icon:5});
                }
            },
        });
    })
</script>
