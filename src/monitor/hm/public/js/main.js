$(function () {
    /**
     * 关闭所有弹窗
     */
    $('.modalClose').click(function () {
        parent.layer.closeAll();
    });
})

/**
 * 获取url中的参数
 * @param url
 * @returns {{}}
 */
function getUrlParams(url) {
    const searchParams = new URLSearchParams(url.search);
    const params = {};
    for (let [key, value] of searchParams) {
        params[key] = value;
    }
    return params;
}

/**
 * 填充表单信息
 */
function fillForm(formId, data) {
    // 遍历所有的查询参数，并填充到表单中
    $.each(params, function(key, value) {
        var input = $(formId).find('[name="' + key + '"]');
        if (input.is('input[type="text"]')) {
            input.val(value);
        } else if (input.is('select')) {
            input.val(value); // 或者根据需要使用.append()来添加<option>
        } else if (input.is('input[type="radio"]')) {
            input.filter('[value="' + value + '"]').prop('checked', true);
        } else if (input.is('input[type="checkbox"]')) {
            input.filter('[value="' + value + '"]').prop('checked', true);
        }
    });
}

/**
 * 表单信息重置为空
 */
function resetForm(formId) {
    $(formId).find('input, textarea, select').val('').prop('selected', false);
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
        "class Foo extends LogicalService{\n\n\tpublic function handle()\n\t{ \n\t\t\n\t}\n}");
    editors[index].moveCursorToPosition({row: 0, column: 0});
}
