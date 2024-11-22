<?php
$cdn = get_cdn().'/hm';
?>
<script src="<?php echo $cdn?>/ace-builds-master/src/ace.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo $cdn?>/ace-builds-master/src/ext-language_tools.js" type="text/javascript" charset="utf-8"></script>

<div class="row">
    <div class="col pr-2">
        <div class="card">
            <div class="card-body">
                <form id="form-logical-blocks-search">
                    <div class="form-group row">
                        <div class="col">
                            <a href="javascript:jump_page({'group':''})" style="text-decoration: none;"><span class="badge
                            @if(request()->input('group') == '') badge-info @endif
                            ">全部</span></a>
                            @foreach($GroupList as $group)
                                <a href="javascript:jump_page({'group':'{{$group->group}}'})" style="text-decoration: none;">
                                    <span class="badge
                                        @if(request()->input('group') == $group->group) badge-info @endif
                                    ">{{$group->group}}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col">
                            <input type="text" name="object_id" placeholder="object_id" class="form-control">
                        </div>
                        <div class="col pl-0">
                            <input type="text" name="name" placeholder="name" class="form-control">
                        </div>
                        <div class="col pl-0">
                            <input type="text" name="label" placeholder="label" class="form-control">
                        </div>
                    </div>
                    <div class="float-right">
                        <a href="javascript:"
                           class="btn btn-link btn-sm"
                           onclick="resetForm('#form-logical-blocks-search')"
                        >重置</a>
                        <a href="javascript:"
                           class="btn btn-link btn-sm logical-blocks-search"
                        >查询</a>
                    </div>
                </form>
                <div class="float-left mb-3">
                    <a href="javascript:" type="button" class="btn btn-outline-primary btn-sm" id="hm-code-create">新增</a>
                    <a href="javascript:"
                       type="button"
                       class="btn btn-outline-primary btn-sm ky-modal"
                       data-title="粘贴逻辑块"
                       data-width="800px"
                       data-height="600px"
                       data-href={{jump_link("/hm/logical-block/paste")}}
                    >粘贴逻辑块</a>
                </div>
                <table class="table table-sm table-hover">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col" style="width: 20px">object_id</th>
                        <th scope="col">name</th>
                        <th scope="col">group</th>
                        <th scope="col">label</th>
                        <th scope="col">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($LogicalBlocks as $k=>$block)
                        <tr>
                            <th scope="row">{{$k+1}}</th>
                            <td class="ellipsis">{{$block->object_id}}</td>
                            <td><a href="javascript:"
                                   class="code-object-item-ky-req"
                                   data-id="{{$block->id}}"
                                >{{$block->name}}</a></td>
                            <td>{{$block->group}}</td>
                            <td>{{$block->label}}</td>
                            <td>
                                <a href="javascript:"
                                   class="copy-logical-block"
                                   data-id="{{$block->id}}"
                                >复制逻辑块</a>
                                <a href="javascript:"
                                   class="ky-req ml-2"
                                   data-href="{{jump_link('/hm/logical-block/copy-new?id='.$block->id)}}"
                                   data-confirm-ky="确定复制【{{$block->name}}】么？ 复制后会新增一个副本"
                                   data-type="POST"
                                >复制</a>
                                <a href="javascript:"
                                   class="ky-req ml-2"
                                   data-confirm-ky="确定删除【{{$block->name}}】么？"
                                   data-type="POST"
                                   data-href={{jump_link('/hm/logical-block/delete?id='.$block->id)}}
                                >删除</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="float-right">
                    {{$LogicalBlocks->withPath(jump_link('/hm/logical-block/index'))->appends(request()->query())->links('pagination::bootstrap-4')}}
                </div>
            </div>
        </div>
    </div>
    <div class="col pl-2">
        <div class="card">
            <div class="card-body">
                <form id="hoo-run-code">
                    <input hidden name="id" id="code-object-id">
                    <div class="form-group">
                        <div class="row">
                            <div class="col">
                                <input type="text" id="code-object-group" name="group" placeholder="group" class="form-control">
                            </div>
                            <div class="col">
                                <input type="text" id="code-object-name" name="name" placeholder="name" class="form-control">
                            </div>
                            <div class="col">
                                <input type="text" id="code-object-label" name="label" placeholder="label" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <textarea id="code-object-text" name="logical_block" hidden></textarea>
                        <pre id="code-object-edit" class="ace_editor" style="min-height:400px">
                            <textarea></textarea>
                        </pre>
                    </div>
                    <div class="float-left">
                        <a href="javascript:"
                           class="btn btn-link btn-sm maxCode"
                        >最大化</a>
                        <a href="javascript:"
                           class="btn btn-link btn-sm formRunCodeSave"
                           id="hm-code-object-save"
                        >保存</a>
                    </div>
                    <div class="float-right">
                        <a href="javascript:"
                           data-from_id="hoo-run-code"
                           class="btn btn-outline-primary btn-sm formRunCodeSubmit"
                           data-href={{jump_link('/hm/logical-block/run')}}
                        >run</a>
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

    /**
     * 页面加载后运行
     */
    $(document).ready(function(){
        //获取当前页面url携带的参数
        var url = window.location.href;
        params = getUrlParams(new URL(url));
        if(params['id']){
            //加载编排数据
            getDetail(params['id']);
        }

        // 遍历所有的查询参数，并填充到表单中
        fillForm('#form-logical-blocks-search',params)

        //初始化新增 编辑表单
        loadForm();
    })
    /**
     * 加载编辑器
     * @type {string}
     */
    var edit_id = "code-object-edit"
    edit_init(edit_id)



    /**
     * 最大化
     */
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
     * 搜索
     */
    $(document).on("click",".logical-blocks-search",function(){
        var formData = $("#form-logical-blocks-search").serializeArray().reduce(function(obj, item) {
                obj[item.name] = item.value;
                return obj;
            }, {});
        jump_page(formData)
    })

    /**
     * 表单初始化
     */
    function loadForm() {
        editorSetDifaultCode(edit_id)
        // 表单置空
        resetForm('#hoo-run-code');
    }

    /**
     * 新增
     */
    $(document).on("click","#hm-code-create",function(){
        layer.confirm('创建前，请先确认当前内容是否已保存！', {
            icon:0,
            closeBtn: 0,
            btn: ['取消','无需保存'] //按钮
        }, function(){
            layer.closeAll();
        }, function(){
            layer.closeAll();
            loadForm()
        });
    })

    /**
     * 复制logical-block
     */
    $(document).on("click",".copy-logical-block",function(){
        id = $(this).attr('data-id')
        $.ajax({
            type:'get',
            url:jump_link('/hm/logical-block/copy?id='+id),
            dataType:"json",//返回数据形式为json
            beforeSend:function(e){

            },
            success:function(result){
                if(result.code == 200){
                    // 放入剪切板
                    copyToClipboard(result.data.logical_block)
                    layer.msg('复制成功')
                }else {
                    layer.msg('复制失败！'+result.message, {icon: 0})
                }
            },
            error: function(xhr, status, error) {

            }
        });
    })


    /**
     * 逻辑块运行
     */
    $(document).on("click",".formRunCodeSubmit",function(){
        var from_id = $(this).attr('data-from_id');
        var href = $(this).attr('data-href');

        $("#code-object-text").val(editors[edit_id].getValue());

        $('#run-code-output').html('<div class="spinner-border text-dark" style="width: 1rem;height: 1rem" role="status"><span class="sr-only">Loading...</span></div>')

        // 获取from表单数据 并转为json格式
        var formData = $("#"+from_id).serializeArray();
        // 遍历
        formData = formData.reduce(function(obj, item) {
            obj[item.name] = item.value;
            return obj;
        }, {});

        // 对数据进行加密   encrypt - 加密方法
        formData['logical_block'] = sm4.encrypt(formData['logical_block'])

        $.ajax({
            type:'post',
            url:href,
            data:formData,
            dataType:"json",//返回数据形式为json
            success:function(result){
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
        var href = '{{jump_link('/hm/logical-block/save')}}';

        $("#code-object-text").val(editors[edit_id].getValue());

        // 获取from表单数据 并转为json格式
        var formData = $("#"+from_id).serializeArray();
        // 遍历
        formData = formData.reduce(function(obj, item) {
            obj[item.name] = item.value;
            return obj;
        }, {});

        // 对数据进行加密   encrypt - 加密方法
        formData['logical_block'] = sm4.encrypt(formData['logical_block'])

        $.ajax({
            type:'post',
            url:href,
            data:formData,
            dataType:"json",//返回数据形式为json
            beforeSend:function(e){
                layer.closeAll();
                index = layer.load(1);
            },
            success: function (result) {
                layer.close(index);
                if(result.code == 200){
                    layer.msg(result.message, {icon: 6, time: 500}, function(){
                        // layer.load(1);
                        //刷新当前页
                        // id = result.data.id
                        // // 在当前链接增加id参数 并刷新 如果链接上已经有id参数 则覆盖
                        // var url = window.location.href;
                        // params = getUrlParams(new URL(url));
                        // params['id'] = id
                        // // 按照?分割字符串
                        // var rote = window.location.href.split('?')[0];
                        // // 将参数和路由转换为url
                        // jump_url = rote + '?' + $.param(params);
                        //
                        // window.location.href = jump_url;

                        jump_page({
                            'id':result.data.id
                        })

                        // // 将当前页面 网址框内路由调整成新路由
                        // window.history.pushState({}, 0, jump_url);
                        //
                        // // inpiut 赋值
                        // $("#code-object-id").val(result.data.id);
                        // // 如果发送的数据中没有id 则认为这是一个新增的请求 需要刷新页面
                        // if(formData['id'] == ''){
                        //     window.location.href = jump_url;
                        // }
                    });
                    calleBack()
                }else{
                    layer.alert(result.message, {icon: 5});
                }
            },
        });
    }

    /**
     * object详情获取
     */
    $(document).on("click",".code-object-item-ky-req",function(event){
        // 阻止事件冒泡，防止按钮点击事件被触发
        event.stopPropagation()
        // 在这里处理行点击的逻辑
        var id = $(this).attr('data-id');
        getDetail(id)
    })

    function getDetail(id){
        var href = "{{jump_link("/hm/logical-block/detail")}}";
        $.ajax({
            type:'get',
            url:href,
            data:{"id":id},
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

                    logical_block = sm4.decrypt(e.data.logical_block)

                    editors[edit_id].setValue(logical_block);
                    editors[edit_id].moveCursorToPosition({row: 0, column: 0});

                }else{
                    layer.alert(e.message,{icon:5});
                }
            },
        });
    }
</script>
