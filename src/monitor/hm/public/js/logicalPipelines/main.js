/**
 * 逻辑线运行
 */
$(document).on("click",".RunLogicalPipeline",function(){
    var href = $(this).attr('data-href');
    var type = "POST";

    $('#run-code-output').html('<div class="spinner-border text-dark" style="width: 1rem;height: 1rem" role="status"><span class="sr-only">Loading...</span></div>')

    $.ajax({
        type:type,
        url:href,
        dataType:"json",//返回数据形式为json
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
 * 编排
 */
$(document).on("click","#logical-pipelines-arrange",function(){
    var href = $(this).attr('data-href');
    var type = $(this).attr('data-type');

    $('#logical-pipelines-arrange-show').html('<div class="spinner-border text-dark" style="width: 1rem;height: 1rem" role="status"><span class="sr-only">Loading...</span></div>')

    $.ajax({
        type:type,
        url:href,
        // dataType:"json",//返回数据形式为json
        success:function(e){
            $('#logical-pipelines-arrange-show').html(e);
            show_edit();
        },
        error: function(xhr, status, error) {
            $("#logical-pipelines-arrange-show").html(xhr.responseText);
        }
    });
})

function show_edit() {
    // 确定有多少个编辑框需要初始化
    var _divArr = document.getElementsByClassName('logical-pipelines-arrange-ace-editor');
    var divLength = _divArr.length;
    for(var i=0;i<divLength;i++){
        edit_init(_divArr[i].id, _divArr[i].getAttribute('data-id'))
    }
    return ;
}

/**
 * 编辑器对象池
 * @type {*[]}
 */
var editors = [];
function edit_init(id,index){
    //初始化编辑器
    editor = ace.edit(id);
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

    //填充内容
    editor.setValue(document.getElementById('code-object-text-'+index).value);

    //设置光标位置
    editor.moveCursorToPosition(cursorPosition);

    editors[index] = editor;
}

/**
 * 逻辑块保存
 */
$(document).on("click",".formRunCodeSave",function(){
    save(this);
})

function save(_this,calleBack=function(){}) {
    var id = $(_this).attr('data-id');
    var from_id = $(_this).attr('data-from_id');
    var href = $(_this).attr('data-href');

    $("#code-object-text-"+id).val(editors[id].getValue());

    $("#"+from_id).ajaxSubmit({
        type:"post",
        url:href,
        dataType: 'json' ,
        beforeSend:function(e){
            layer.closeAll();
            index = layer.load(1);
        },
        success: function (result) {
            layer.close(index);
            if(result.code == 200){
                layer.msg(result.message,{icon:6});
                calleBack();
            }else{
                layer.alert(result.message, {icon: 5});
            }
        },
    });
}
