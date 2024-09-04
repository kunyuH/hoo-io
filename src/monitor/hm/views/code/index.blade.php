<?php
$cdn = get_cdn();
?>
<script src="<?php echo $cdn?>/ace-builds-master/src/ace.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo $cdn?>/ace-builds-master/src/ext-language_tools.js" type="text/javascript" charset="utf-8"></script>

<div class="row">
    <div class="col-2">
        <div class="card">
            <div class="card-header">
                logical block
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
                    <div class="form-group">
                        <textarea id="code-object-text" name="value" hidden></textarea>
                        <pre id="code-object-edit" class="ace_editor" style="min-height:400px">
                            <textarea></textarea>
                        </pre>
                    </div>
                    <div class="float-left">
                        <a href="javascript:"
                           class="btn btn-outline-primary btn-sm maxCode"
                        >最大化</a>
                    </div>
                    <div class="float-right">
                        <a href="javascript:"
                           data-from_id="hoo-run-code"
                           class="btn btn-outline-primary btn-sm formRunCodeSubmit"
                           data-href={{jump_link('/hm/run-code')}}
                        >Run</a>
                        <a href="javascript:"
                           class="btn btn-outline-success btn-sm formRunCodeSave"
                           id="hm-code-object-save"
                        >Create</a>
                        <a href="javascript:"
                           class="btn btn-danger btn-sm formCodeDelete"
                           id="hm-code-object-delete"
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
<style>
    .hoo-layer-open .layui-layer-title{
        display: none;
    }
    .hoo-layer-open .layui-layer-setwin{
        background-color: #f0f8ff47;
        right: 25px;
        border-radius: .25rem;
        height: 14px;
        width: 33px;
    }
    .hoo-layer-open .layui-layer-content{
        height: 100% !important;
    }
    .hoo-layer-open .layui-layer-content pre{
        margin-bottom: 0;
    }
</style>
<script>
    //初始化编辑器
    editor = ace.edit("code-object-edit");
    //设置风格和语言（更多风格和语言，请到github上相应目录查看）
    theme = "clouds"
    theme = "twilight"
    // theme = "ambiance"
    // theme = "solarized_light"
    language = "php"
    // language = "php_laravel_blade"
    editor.setTheme("ace/theme/" + theme);
    editor.session.setMode("ace/mode/" + language);
    //字体大小
    editor.setFontSize(14);
    //设置只读（true时只读，用于展示代码）
    editor.setReadOnly(false);
    editor.highlightActiveLine = true;
    //自动换行,设置为off关闭
    editor.setOption("wrap", "free")
    //启用提示菜单
    ace.require("ace/ext/language_tools");
    editor.setOptions({
        enableBasicAutocompletion: true,
        enableSnippets: true,
        enableLiveAutocompletion: true
    });
    // 获取光标位置
    var cursorPosition = editor.getCursorPosition();

    $(function(){
        loadCodeObjectList();
        loadForm();
    })

    $(document).on("click",".maxCode",function(){
        layer.open({
            skin: 'hoo-layer-open',
            shade: 0.8,
            type: 1,
            area: ['100%', '100%'], // 全屏
            maxmin: false,
            scrollbar: true,
            content: $('#code-object-edit'),
            success: function(layero, index){
                document.getElementById("code-object-edit").style.height = '100%';
                // 绑定键盘事件
                $(document).on('keydown', function(e){
                    e = e || window.event;
                    var key = e.keyCode || e.which; // 获取按键的keyCode
                    if(key == 27){ // 27是ESC按键的keyCode
                        layer.close(index); // 关闭弹层
                    }
                });
            },
            cancel: function(index, layero){
                document.getElementById("code-object-edit").style.height = "500px";
            },
            end: function(){
                // 移除键盘事件监听
                $(document).off('keydown');
            }
        });
    });

    /**
     * 表单初始化
     */
    function loadForm() {
        $("#code-object-name").val('');
        $("#code-object-group").val('');
        $("#code-object-label").val('');

        $("#code-object-id").val('');
        // 改变按钮内容
        $("#hm-code-object-save").html('Create');
        editor.setValue("<\?php\n\n" +
            "class Foo{\n\n\tpublic function run()\n\t{ \n\t\t\n\t}\n}");
        editor.moveCursorToPosition(cursorPosition);
        $("#code-object-text").val('');
    }

    /**
     * logical block 列表加载
     */
    function loadCodeObjectList() {
        $.ajax({
            type:'get',
            url:'{{jump_link("/hm/code/list")}}',
            dataType:"json",//返回数据形式为json
            beforeSend:function(e){},
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
        layer.confirm('点击新增后，当前逻辑块会丢失，请先确认是否已保存！', {
            icon:0,
            closeBtn: 0,
            btn: ['保存','无需保存'] //按钮
        }, function(){
            save(function () {
                layer.closeAll();
                loadForm()
            })
        }, function(){
            layer.closeAll();
            loadForm()
        });
    })

    // /**
    //  * CodeMirror 实例
    //  * @type {CodeMirror|*}
    //  */
    // var editor = CodeMirror.fromTextArea(document.getElementById("code-object-text"), {
    //     lineNumbers: true,
    //     matchBrackets: true,
    //     mode: "application/x-httpd-php",
    //     indentUnit: 4,
    //     indentWithTabs: true,
    //     theme: 'seti',
    // });

    /**
     * 逻辑块运行
     */
    $(document).on("click",".formRunCodeSubmit",function(){
        var from_id = $(this).attr('data-from_id');
        var href = $(this).attr('data-href');

        $("#code-object-text").val(editor.getValue());

        $('#run-code-output').html('<div class="spinner-border text-dark" style="width: 1rem;height: 1rem" role="status"><span class="sr-only">Loading...</span></div>')
        $("#"+from_id).ajaxSubmit({
            type:"post",
            url:href,
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
     * 逻辑块保存
     */
    $(document).on("click",".formRunCodeSave",function(){
        save();
    })

    function save(_this,calleBack=function(){}) {
        var from_id = 'hoo-run-code';
        var url = '{{jump_link('/hm/code/save')}}';
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
                    calleBack()
                }else{
                    layer.alert(result.message, {icon: 5});
                }
            },
        });
    }

    /**
     * 逻辑块删除
     */
    $(document).on("click",".formCodeDelete",function(){
        deleted();
    })

    function deleted(){
        var type = "POST";
        var href = '{{jump_link('/hm/code/delete')}}';
        var id = $('#code-object-id').val()
        if (id == ''){
            layer.alert('请选择要删除的对象！', {icon: 5});
            return false;
        }

        layer.confirm('确定要删除么', {
            btn: ['删除', '取消'], //按钮
            icon: 3
        }, function () {
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
        });
    }

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
                    editor.setValue(e.data.logical_block);
                    editor.moveCursorToPosition(cursorPosition);
                    $("#hm-code-object-save").html('update');
                }else{
                    layer.alert(e.message,{icon:5});
                }
            },
        });
    })
</script>
