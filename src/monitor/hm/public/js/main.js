// 页面加载后运行
$(function () {
    /**
     * 关闭所有弹窗
     */
    $('.modalClose').click(function () {
        parent.layer.closeAll();
    });
    // json格式数据格式化展示
    json_show()
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

    // 为编辑器添加键盘绑定，监听Ctrl+D
    editor.commands.addCommand({
        name: 'duplicateLine',
        bindKey: {
            win: 'Ctrl-D',
            mac: 'Command-D',
            sender: 'editor|cli'
        },
        exec: function(editor) {
            // 获取当前行的位置
            var cursorPosition = editor.getCursorPosition();
            var currentLine = cursorPosition.row;

            // 获取当前行的内容
            var lineContent = editor.session.getLine(currentLine);

            // 在下一行插入当前行的内容
            editor.session.insert({row: currentLine + 1, column: 0}, lineContent + '\n');

            // 将光标移动到新插入行的末尾
            editor.moveCursorTo(currentLine + 1, lineContent.length);
        }
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

let key = 'bzlaxbrww2nkczqb'
let sm4Config = {
    key: key, // 密钥
    mode: 'ecb', // 加密的方式有两种，ecb和cbc两种
    cipherType: 'base64'
}
var sm4 = new Sm4js(sm4Config)
// // 对数据进行加密   encrypt - 加密方法
// let encrypted = sm4.encrypt(formData['logical_block'])
//
// console.log(encrypted)
// 对数据进行解密   decrypt - 解密方法
// let decrypted = sm4.decrypt(encrypted)

/***************json格式化展示********************/
function json_show(){
    //格式化json @zhangxh
    var formatJsonForNotes = function formatJson(jsonString) {
        try {
            const json = JSON.parse(jsonString);
            return JSON.stringify(json, null, 2); // 使用2个空格进行缩进
        } catch (e) {
            return jsonString; // 如果不是有效的JSON，则返回原始字符串
        }
    }
    json_str = $('.json-show')
    // 遍历
    json_str.each(function () {
        $(this).text(formatJsonForNotes($(this).text()));
    })
}

