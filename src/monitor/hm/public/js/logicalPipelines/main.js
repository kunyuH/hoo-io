/**
 * 页面加载后运行
 */
$(document).ready(function(){
    //获取当前页面url携带的参数
    var url = window.location.href;
    params = getUrlParams(new URL(url));
    if(params['pipeline_id']){
        //加载编排数据
        getArrangeList(params['pipeline_id']);
    }
})
/**
 * 最大化
 */
$(document).on("click",".maxCode",function(){

    code_id = 'code-object-edit-'+$(this).attr('data-id');
    //code-object-edit-14
    console.log(code_id);

    layer.open({
        // skin: 'hoo-layer-open',
        title: 'logical block',
        shade: 0.8,
        type: 1,
        // area: ['100%', '100%'], // 全屏
        area: ['800px', '500px'], // 全屏
        maxmin: true,
        scrollbar: true,
        content: $('#'+code_id),
        success: function(layero, index){
            document.getElementById(code_id).style.height = '100%';
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
            document.getElementById(code_id).style.height = "200px";
        },
        end: function(){
            // 移除键盘事件监听
            $(document).off('keydown');
        }
    });
});

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
$(document).on("click",".logical-pipelines-arrange",function(){
    var id = $(this).attr('data-id');
    getArrangeList(id)
})

/**
 * 获取逻辑线的编排列表
 * @param id
 */
function getArrangeList(id){
    var href = jump_link('/hm/logical-pipelines/arrange?id='+id);

    $('#logical-pipelines-arrange-show').html('<div class="spinner-border text-dark" style="width: 1rem;height: 1rem" role="status"><span class="sr-only">Loading...</span></div>')

    $.ajax({
        type:'get',
        url:href,
        // dataType:"json",//返回数据形式为json
        success:function(result){
            //如果返回的是json 则转为字符串
            if(typeof result == 'object'){
                result = JSON.stringify(result)
                result = result.replace(/\\n/g, "<br>").replace(/\\t/g, "&nbsp;&nbsp;&nbsp;&nbsp;")
                $("#logical-pipelines-arrange-show").html(result);
            }else{
                $("#logical-pipelines-arrange-show").html(result);
            }
            show_edit();
        },
        error: function(xhr, status, error) {
            $("#logical-pipelines-arrange-show").html(xhr.responseText);
        }
    });
}

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
