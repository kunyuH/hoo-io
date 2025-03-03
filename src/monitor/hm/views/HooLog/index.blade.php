<?php
$cdn = get_cdn().'/hm';
?>

<div class="row">

    <div class="col-3">
        <div class="card">
            <div class="card-body">
                <div id="ID-tree-demo-showLine"></div>
            </div>
        </div>
    </div>
    <div class="col-9">
        <div class="card">
            <div class="card-body table-responsive">
                <form id="form-hoo-log-search">
                    <div class="form-group">
                        <div class="row">
                            <div class="col">
                                <input id="hoo-input-path" type="text" name="path" placeholder="path" class="form-control">
                            </div>
                            <div class="col">
                                <input id="hoo-input-keyword" type="text" name="keyword" placeholder="keyword" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="float-right">
                        <a href="javascript:"
                           class="btn btn-link btn-sm"
                           onclick="resetForm('#form-hoo-log-search')"
                        >重置</a>
                        <a href="javascript:"
                           class="btn btn-link btn-sm hoo-log-search"
                        >查询</a>
                    </div>
                </form>
                <hr>
                <div id="hoo-log-page"></div>
                <div id="hoo-log-show"></div>
            </div>
        </div>
    </div>
</div>
<style>
    .log-date{
        padding: 2px 5px;
        border-radius: 3px;
        font-size: 0.9em;
        /*background-color: #00ff9c;*/
        color: #00ff9c;
        font-weight: bold;
    }
    .log-level{
        padding: 2px 5px;
        border-radius: 3px;
        font-size: 0.9em;
        font-weight: bold;
    }
    .log-level-info{
        /*background-color: #83a598;*/
        color: #83a598;
    }
    .log-level-debug{
        /*background-color: #458588;*/
        color: #458588;
    }
    .log-level-error{
        /*background-color: #fb4934;*/
        color: #fb4934;
    }
</style>
<script>
    /**
     * 页面加载后运行
     */
    $(document).ready(function(){
        //加载文件树
        loadPathTree();
    })

    /**
     * 加载文件树
     */
    function loadPathTree(){
        $.ajax({
            type:'get',
            url:jump_link('/hm/hoo-log/get-path-tree'),
            dataType:"json",//返回数据形式为json
            beforeSend:function(e){
                $('#ID-tree-demo-showLine').html('<div class="spinner-border text-dark" style="width: 1rem;height: 1rem" role="status"><span class="sr-only">Loading...</span></div>')

            },
            success:function(result){
                $("#ID-tree-demo-showLine").html('');
                layui.use(function(){
                    var tree = layui.tree;
                    var data = result.data;
                    // 渲染
                    tree.render({
                        elem: '#ID-tree-demo-showLine',
                        data: data,
                        showLine: true,  // 是否开启连接线
                        onlyIconControl: false,
                        edit: ['del'],
                        click: function(obj){

                            $('#hoo-input-path').attr('value',obj.data.file_path)

                            // search_log(obj.data.file_path)
                            // 触发按钮点击
                            $('.hoo-log-search').click()
                        },
                        operate: function(obj){
                            var type = obj.type; // 得到操作类型：add、edit、del
                            var data = obj.data; // 得到当前节点的数据
                            var elem = obj.elem; // 得到当前节点元素

                            // Ajax 操作
                            var file_path = data.file_path; // 得到节点索引
                            if(type === 'del'){ // 增加节点
                                del_log(file_path)
                            };
                        }
                    });
                });
            },
            error: function(xhr, status, error) {
                $("#ID-tree-demo-showLine").html('加载失败！');
            }
        });
    }


    /**
     * 搜索
     */
    $(document).on("click",".hoo-log-search",function(){
        var formData = $("#form-hoo-log-search").serializeArray();
        // 遍历
        formData = formData.reduce(function(obj, item) {
            obj[item.name] = item.value;
            return obj;
        }, {});

        search_log(formData['path'],formData['keyword'])

    })

    function search_log(path,keyword='',limit=10,page=1) {
        $.ajax({
            type:'get',
            url:jump_link('/hm/hoo-log/search'),
            data:{path:path,keyword:keyword,limit:limit,page:page},
            // dataType:"json",//返回数据形式为json
            beforeSend:function(e){
                $('#hoo-log-show').html('<div class="spinner-border text-dark" style="width: 1rem;height: 1rem" role="status"><span class="sr-only">Loading...</span></div>')

            },
            success:function(result){
                if(result.code==200){
                    html = '<div style="background-color: #141414;border-radius: .25rem;padding: 10px 10px;color: #dddddd;">'
                    $("#hoo-log-show").html('<div style="background-color: #141414;border-radius: .25rem;padding: 0 15px;color: #dddddd;">')
                    // 遍历
                    for (var key in result.data.list) {

                        log_txt = result['data']['list'][key]
                            // 替换日期部分
                            .replace(/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/, '<span class="log-date">$1</span>')
                            // 替换日志等级部分
                            .replace(/local\.(INFO):/, '<span class="log-level log-level-info">$&</span>')
                            .replace(/local\.(DEBUG):/, '<span class="log-level log-level-debug">$&</span>')
                            .replace(/local\.(ERROR):/, '<span class="log-level log-level-error">$&</span>');


                        html += '<div style="margin: 5px 0px;">'+log_txt+'</div>'
                    }
                    html += '</div>'
                    $("#hoo-log-show").html(html)

                    loadLogPage(result.data.count,result.data.page,result.data.limit)

                }else{
                    $("#hoo-log-show").html('加载失败！'+result.message);
                }
            },
            error: function(xhr, status, error) {
                $("#hoo-log-show").html('加载失败！');
            }
        });
    }

    function loadLogPage(count,curr=1,limit=50){
        layui.use(function(){
            var laypage = layui.laypage;
            // 自定义排版
            laypage.render({
                elem: 'hoo-log-page',
                count: count,
                curr: curr,
                limit: limit,
                limits: [10, 50, 100, 300, 500],
                layout: ['count', 'prev', 'page', 'next', 'limit', 'refresh', 'skip'], // 功能布局
                jump: function(obj, first){
                    // 首次不执行
                    if(!first){
                        var formData = $("#form-hoo-log-search").serializeArray();
                        // 遍历
                        formData = formData.reduce(function(obj, item) {
                            obj[item.name] = item.value;
                            return obj;
                        }, {});
                        search_log(formData['path'],formData['keyword'],obj.limit,obj.curr)
                    }
                }
            });
        });
    }

    function del_log(path) {
        $.ajax({
            type:'get',
            url:jump_link('/hm/hoo-log/del'),
            data:{path:path},
            // dataType:"json",//返回数据形式为json
            beforeSend:function(e){
                layer.closeAll();
                index = layer.load(1);
            },
            success:function(result){
                layer.close(index);
                if(result.code==200){
                    layer.msg(result.message,{icon: 1});
                }else{
                    layer.alert(result.message,{icon:5});
                }
            },
            error: function(xhr, status, error) {
                $("#hoo-log-show").html('加载失败！');
            }
        });
    }

</script>
