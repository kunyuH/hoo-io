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
    var formatJsonForNotes = function(json, options) {
        var reg = null,
            formatted = '',
            pad = 0,
            PADDING = '  '; // （缩进）可以使用'\t'或不同数量的空格
        // 可选设置
        options = options || {};
        // 在 '{' or '[' follows ':'位置移除新行
        options.newlineAfterColonIfBeforeBraceOrBracket = (options.newlineAfterColonIfBeforeBraceOrBracket === true) ? true : false;
        // 在冒号后面加空格
        options.spaceAfterColon = (options.spaceAfterColon === false) ? false : true;
        // 开始格式化...
        if (typeof json !== 'string') {
            // 确保为JSON字符串
            json = JSON.stringify(json);
        } else {
            //已经是一个字符串，所以解析和重新字符串化以删除额外的空白
            json = JSON.parse(json);
            json = JSON.stringify(json);
        }
        // 在花括号前后添加换行
        reg = /([\{\}])/g;
        json = json.replace(reg, '\r\n$1\r\n');
        // 在方括号前后添加新行
        reg = /([\[\]])/g;
        json = json.replace(reg, '\r\n$1\r\n');
        // 在逗号后添加新行
        reg = /(\,)/g;
        json = json.replace(reg, '$1\r\n');
        // 删除多个换行
        reg = /(\r\n\r\n)/g;
        json = json.replace(reg, '\r\n');
        // 删除逗号前的换行
        reg = /\r\n\,/g;
        json = json.replace(reg, ',');
        // 可选格式...
        if (!options.newlineAfterColonIfBeforeBraceOrBracket) {
            reg = /\:\r\n\{/g;
            json = json.replace(reg, ':{');
            reg = /\:\r\n\[/g;
            json = json.replace(reg, ':[');
        }
        if (options.spaceAfterColon) {
            reg = /\:/g;
            json = json.replace(reg, ': ');
        }
        $.each(json.split('\r\n'), function(index, node) {
            var i = 0,
                indent = 0,
                padding = '';
            if (node.match(/\{$/) || node.match(/\[$/)) {
                indent = 1;
            } else if (node.match(/\}/) || node.match(/\]/)) {
                if (pad !== 0) {
                    pad -= 1;
                }
            } else {
                indent = 0;
            }
            for (i = 0; i < pad; i++) {
                padding += PADDING;
            }
            formatted += padding + node + '\r\n';
            pad += indent;
        });
        return formatted;
    };
    json_str = $('.json-show')
// 遍历
    json_str.each(function () {
        $(this).text(formatJsonForNotes($(this).text()));
    })
}

