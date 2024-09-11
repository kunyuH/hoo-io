$(function () {
    $('.modalClose').click(function () {
        parent.layer.closeAll();
    });
})

function getUrlParams(url) {
    const searchParams = new URLSearchParams(url.search);
    const params = {};
    for (let [key, value] of searchParams) {
        params[key] = value;
    }
    return params;
}

/**
 * 编辑器对象池
 * @type {*[]}
 */
var editors = [];
function edit_init(index){
    //初始化编辑器
    editor = ace.edit(index);
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

    editors[index] = editor;
}

/**
 * 设置默认代码
 * @param index
 */
function editorSetDifaultCode(index){
    editors[index].setValue("<\?php\n\n" +
        "use hoo\\io\\monitor\\hm\\Services\\LogicalService;\n\n" +
        "class Foo extends LogicalService{\n\n\tpublic function run()\n\t{ \n\t\t\n\t}\n}");
    editors[index].moveCursorToPosition({row: 0, column: 0});
}
