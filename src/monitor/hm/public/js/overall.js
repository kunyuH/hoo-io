/**
 * clss触发弹窗
 */
$(document).on("click",".ky-modal",function(){
    var EditRule = $(this).attr('data-href');
    var EditTitle = $(this).attr('data-title');
    var width = $(this).attr('data-width');
    var height = $(this).attr('data-height');
    if(!width){
        width = '800px';
    }
    if(!height){
        height = '500px';
    }
    open_layer(EditTitle,EditRule,width,height);
});

/**
 * 弹窗单选科目
 */
$(document).on("click",".ky-modal-select-course",function(){
    var redirect = $(this).attr('data-href');
    var data_prefix = $(this).attr('data-prefix');
    var data_type = $(this).attr('data-type');
    var data_data = $(this).attr('data-data');
    var data_request_type = $(this).attr('data-request-type');
    var options = $(this).attr('options');
    options = encodeURIComponent(options);
    data_data = encodeURIComponent(data_data);
    var EditRule = data_prefix+'/course/select?data='+data_data+'&redirect='+redirect+'&type='+data_type+'&request_type='+data_request_type+'&options='+options;

    var EditTitle = '请选择';
    var width = '500px';
    var height = '500px';

    open_layer(EditTitle,EditRule,width,height);
});

/**
 * 弹窗
 * @param EditTitle
 * @param EditRule
 * @param width
 * @param height
 */
function open_layer(EditTitle,EditRule,width,height){
    if(!width){
        width = '800px';
    }
    if(!height){
        height = '500px';
    }
    layer.open({
        title: EditTitle,
        shade: 0.8,
        type: 2,
        area: [width, height],
        maxmin: true,
        scrollbar: false,
        content: EditRule
    });
}

/**
 * ajax请求
 */
$(document).on("click",".ky-req",function(){
    var type = $(this).attr('data-type');
    var href = $(this).attr('data-href');
    var params = $(this).attr('data-params');
    if(params){
        params = JSON.parse(params);
    }
    var confirm = $(this).attr('data-confirm-ky');

    if(confirm){
        layer.confirm(confirm, {
            btn: ['确定','取消'], //按钮
            icon: 3
        }, function(){
            kyReq(type,href,params)
        });
    }else{
        kyReq(type,href,params)
    }
});
function kyReq(type,href,params) {
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
                if(e.data.open_type == 1){
                    layer.confirm(e.message, {
                        icon:6,
                        closeBtn: 0,
                        btn: ['确定'] //按钮
                    }, function(){
                        layer.load(1);//loading
                        if(e.data.type == 1){ //跳转到指定页面
                            window.location.href = result.data.redirect_uri;
                        }else if(e.data.type == 2){ //关闭弹窗刷新
                            parent.layer.close(parent.layer.getFrameIndex(window.name));
                            window.parent.location.reload();
                        }else if(e.data.type == 7){ //关闭弹窗不刷新
                            layer.closeAll();
                        }else if(e.data.type == 4){ //刷新
                            history.go(-1);
                        }else{                      //返回上一页
                            window.location.reload();
                        }
                    });
                }else{
                    layer.msg(e.message, {icon: 6, time: 500}, function(){
                        //layer.load(1);//loading
                        if(e.data.type == 1){ //跳转到指定页面
                            layer.load(1);
                            window.location.href = result.data.redirect_uri;
                        }else if(e.data.type == 2){ //modal后 关闭弹窗刷新
                            layer.load(1);
                            parent.layer.close(parent.layer.getFrameIndex(window.name));
                            window.parent.location.reload();
                        }else if(e.data.type == 3){ //modal后 关闭弹窗不刷新
                            layer.load(1);
                            parent.layer.close(parent.layer.getFrameIndex(window.name));
                        }else if(e.data.type == 7){ //关闭弹窗不刷新
                            layer.closeAll();
                        }else if(e.data.type == 4){ //刷新
                            layer.load(1);
                            history.go(-1);
                        }else{                      //返回上一页
                            layer.load(1);
                            window.location.reload();
                        }
                    });
                }
            }else{
                layer.alert(e.message,{icon:5});
            }
        },
    });
}


/**
 * 上传图片是选中图片后触发  用于显示
 * @param file
 * @param img_id
 */
function selectImage(file,img_id){
    if(!file.files || !file.files[0]){
        return;
    }
    var reader = new FileReader();
    reader.onload = function(evt){
        document.getElementById(img_id).src = evt.target.result;
    };
    reader.readAsDataURL(file.files[0]);
}
//点击弹窗放大图片
function checkImg(imgs,max_width=0,max_height = 0){

    // //去除图片链接后面的参数 （此举是为了点击是缩略图时候也能正常放大）
    // var src = imgs.src.substring(0, imgs.src.indexOf('?'));
    var src = imgs.src;

    if(!src){
        src = imgs.src;
    }
    var img = new Image();
    img.src = src;
    // 当 img 加载完毕后触发事件
    layer.load(1);
    img.onload = function () {
        layer.closeAll('loading');
        //最大宽度
        if(max_width == 0){
            max_width = $(document.body).width()*0.9;
        }
        //最大高度
        if(max_height == 0){
            max_height = $(document.body).height()*0.9;
        }

        //设宽度为最大高度  计算高度
        var width = max_width;
        var height = max_width/img.width * img.height;
        //true 不成立
        if(height > max_height){
            //设高度为最大高度  计算宽度
            height = max_height;
            width = max_height/img.height * img.width;
        }

        layer.open({
            type: 1,
            title: false,
            closeBtn: 0,
            // area: ['auto'],
            // area: ['auto'],
            area: [width+'px'],
            skin: 'layui-layer-nobg', //没有背景色
            shadeClose: true,
            content: '<img style="width: 100%;" src='+img.src+' >'
        });
    };
}


/**
 * 通用from表单提交
 * @param from_id
 */
$(document).on("click",".formSubmit",function(){

    var from_id = $(this).attr('data-from_id');
    var url = $(this).attr('data-href');
    var confirm = $(this).attr('data-confirm-ky');

    if(confirm) {
        layer.confirm(confirm, {
            btn: ['确定', '取消'], //按钮
            icon: 3
        }, function () {
            formSubmit(from_id,url,$(this));
        });
    }else{
        formSubmit(from_id,url,$(this));
    }
})

function formSubmit(from_id,url='',_this) {
    //验证表单
    // if(!validateForm(from_id)){
    //     return;
    // }
    layer.load(1); //loading
    $("#"+from_id).ajaxSubmit({
        type:"post",
        url:url,
        dataType: 'json' ,
        success: function (result) {
            layer.closeAll('loading'); //关闭loading
            console.log(result.code)
            console.log(result.type)
            if(result.code == 200){
                if(result.data.type == 3){   //保存并弹出新的窗口
                    var EditRule = result.data.redirect_uri;
                    var EditTitle = _this.attr('data-title');
                    var width = _this.attr('data-width');
                    var height = _this.attr('data-height');
                    if(!width){
                        width = '800px';
                    }
                    if(!height){
                        height = '500px';
                    }
                    open_layer(EditTitle,EditRule,width,height);
                }else if(result.data.type == 11){ //直接跳转到指定页面
                    layer.msg(result.message+"正在加载...", {icon: 16,shade: 0.01,time:"-1"});
                    window.location.href = result.data.redirect_uri;
                }else{
                    layer.confirm(result.message, {
                        icon:6,
                        closeBtn: 0,
                        btn: ['确定'] //按钮
                    }, function(index){
                        layer.load(1);//loading
                        if(result.data.type == 1){ //跳转到指定页面
                            layer.msg(result.message+"正在加载...", {icon: 16,shade: 0.01,time:"-1"});
                            window.location.href = result.data.redirect_uri;
                        }else if(result.data.type == 2){ //关闭弹窗刷新
                            parent.layer.close(parent.layer.getFrameIndex(window.name));
                            window.parent.location.reload();
                        }else if(result.data.type == 4){ //刷新
                            window.parent.location.reload();
                        }else if(result.data.type == 5){ //不做操作
                            layer.closeAll('loading'); //关闭loading
                            layer.close(index);
                        }else if(result.data.type == 6){ //关闭窗口跳转新链接
                            parent.layer.close(parent.layer.getFrameIndex(window.name));
                            layer.msg(result.message+"正在加载...", {icon: 16,shade: 0.01,time:"-1"});
                            window.parent.location.href = result.data.redirect_uri;
                        }else if(result.data.type == 7){ //关闭弹窗不刷新
                            parent.layer.close(parent.layer.getFrameIndex(window.name));
                        }else{                      //返回上一页
                            // console.log('aa')
                            // history.go(-1);
                            history.back()
                        }
                    });
                }

            }else{
                layer.alert(result.message, {icon: 5});
            }
        }
    });
}


/**
 * ajax请求提交前验证机构超管手机
 */
$(document).on("click",".ky-req-v",function(){
    var req_type = $(this).attr('data-type');

    var href = $(this).attr('data-href');
    var params = $(this).attr('data-params');
    if(params){
        params = JSON.parse(params);
    }else{
        params = [];
    }
    var confirm = $(this).attr('data-confirm-ky');

    href = encodeURIComponent(href);
    var href_v = $(this).attr('data-href-v')+'?type=ajax&url='+href+'&req_type='+req_type+'&params='+params;

    layer.confirm(confirm, {
        btn: ['确定','取消'], //按钮
        icon: 3,
        closeBtn: 0
    }, function(index){
        layer.close(index);
        open_layer('验证',href_v,'450px','223px');
    });
});

/**
 * 表单提交带表单验证   与 提交前验证机构超管手机
 */
$(document).on("click",".formSubmitV",function(){
    //获取信息
    var from_id = $(this).attr('data-from_id');
    var EditRule = $(this).attr('data-href-v')+'?type=form&from_id='+from_id;
    var EditTitle = $(this).attr('data-title');
    var width = $(this).attr('data-width');
    var height = $(this).attr('data-height');
    var confirm = $(this).attr('data-confirm-ky');
    if(!width){
        width = '450px';
    }
    if(!height){
        height = '223px';
    }
    if(!EditTitle){
        EditTitle = '验证';
    }
    //验证表单
    if(!validateForm(from_id)){
        return;
    }
    layer.confirm(confirm, {
        btn: ['确定','取消'], //按钮
        icon: 3,
        closeBtn: 0
    }, function(index){
        layer.close(index);
        open_layer(EditTitle,EditRule,width,height);
    });
});




/**
 * 验证表单
 * @param from_id
 * @returns {boolean}
 */
function validateForm(from_id) {

    var $form = $("#"+from_id);
    var data = $form.data("yiiActiveForm");

    // console.log(data)

    $.each(data.attributes,function() {
        this.status = 3;
    });
    $form.yiiActiveForm("validate");

    var error = '';
    $('.help-block').each(function(){
        error = error + $(this).text();
    });
    //true:表单验证未通过
    if(error){
        layer.alert(error, {icon: 5});
        return false;
    }
    return true;
}

/**
 * form表单项设置时候需要展示
 * @param model   注意都是小写
 * @param field_names  只是字段
 * @param is_show
 */
function form_field(model,field_names,is_show) {
    for(var i=0;i<field_names.length;i++){
        var classs = '.field-'+model+'-'+field_names[i];
        var ids = '#'+model+'-'+field_names[i];

        if(is_show){
            $(ids).removeAttr("disabled");
            $(classs).show();
        }else{
            $(ids).attr("disabled","disabled")
            $(classs).hide();
        }
    }
}




