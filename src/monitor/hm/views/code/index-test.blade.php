<?php
$cdn = get_cdn().'/hm';
?>
<!--导入js库-->

<script src="{{$cdn}}/ace-builds-master/src/ace.js" type="text/javascript" charset="utf-8"></script>
<script src="{{$cdn}}/ace-builds-master/src/ext-language_tools.js" type="text/javascript" charset="utf-8"></script>
<div class="row">
    <div class="col-12">
        <!--【特别注意】代码输入框，务必设置高度，否则无法显示 -->
        <!--【特别注意】pre标签和textarea标签之间不要有空格或换行，这些元素都会当着编辑器内容的一部分，造成出现“开头多出一些空格字符”的怪现象！！！-->
        <pre id="code" class="ace_editor" style="min-height:400px">
            <textarea class="ace_text-input ">
                <\?php
use App\Services\ElectronicHealthCard\Yh\YhService;
class Foo{
    public function run(){
        //Cache::forget('electronic_health_card_token');
        //Cache::forget('yh-user');
        $idNoType = '10';
        $idNo = '33038219970203591X';
        //$idNo = '330127197505253622';
        //$idNo = '360481198611291010';
        $a = new YhService();
        $w = $a->mainIndexQuery($idNoType,$idNo);
        var_dump($w);
    }
}
         </textarea>
        </pre>
    </div>
    <a href="javascript:"
       class="btn btn-outline-success formRunCodeSave"
    >最大化</a>
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
    editor = ace.edit("code");

    // 设置编辑器高度 全屏
    // editor.set
    editor.setOption("maxHeight", "900px");


    $(document).on("click",".formRunCodeSave",function(){

        layer.open({
            skin: 'hoo-layer-open',
            shade: 0.8,
            type: 1,
            area: ['100%', '100%'], // 全屏
            maxmin: false,
            scrollbar: true,
            content: $('#code'),
            success: function(layero, index){
                document.getElementById("code").style.height = '100%';
                $('.layui-layer-content').css('height','100%');
                $('.layui-layer-content pre').css('margin-bottom','0');
            },
            cancel: function(index, layero){
                document.getElementById("code").style.height = "400px";
            }

        });
    });

    // $0.requestFullscreen()


    //设置风格和语言（更多风格和语言，请到github上相应目录查看）
    theme = "clouds"
    theme = "twilight"
    // theme = "ambiance"
    // theme = "solarized_light"
    language = "php"
    language = "php_laravel_blade"
    editor.setTheme("ace/theme/" + theme);
    editor.session.setMode("ace/mode/" + language);

    //字体大小
    editor.setFontSize(16);

    //设置只读（true时只读，用于展示代码）
    editor.setReadOnly(false);

    //自动换行,设置为off关闭
    editor.setOption("wrap", "free")

    //设置内容
    // editor.setValue("console.log('Hello, world!');");

    //获取内容
    var content = editor.getValue();

    //启用提示菜单
    ace.require("ace/ext/language_tools");
    editor.setOptions({
        enableBasicAutocompletion: true,
        enableSnippets: true,
        enableLiveAutocompletion: true
    });

    // 查找并替换文本
    editor.findAndReplace({
        find: "search text", // 要查找的文本
        replace: "replacement text", // 要替换的文本
        backwards: false,
        wrap: true,
        caseSensitive: false,
        wholeWord: false,
        regExp: false
    });

</script>
