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
                            <div class="col">
                                <input id="hoo-input-limit" type="text" name="limit" placeholder="limit" class="form-control">
                            </div>
                            <div class="col">
                                <input id="hoo-input-offset" type="text" name="offset" placeholder="offset" class="form-control">
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
                <div id="hoo-log-show"></div>

            </div>
        </div>
    </div>
</div>

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
                            $('#hoo-input-limit').attr('value',10)
                            $('#hoo-input-offset').attr('value',0)

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
                                //返回 key 值
                                console.log("删除节点",file_path)
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

        search_log(formData['path'],formData['keyword'],formData['limit'],formData['offset'])

    })

    function search_log(path,keyword='',limit='',offset='') {
        $.ajax({
            type:'get',
            url:jump_link('/hm/hoo-log/search'),
            data:{path:path,keyword:keyword,limit:limit,offset:offset},
            // dataType:"json",//返回数据形式为json
            beforeSend:function(e){
                $('#hoo-log-show').html('<div class="spinner-border text-dark" style="width: 1rem;height: 1rem" role="status"><span class="sr-only">Loading...</span></div>')

            },
            success:function(result){
                //如果返回的是json 则转为字符串
                if(typeof result == 'object'){
                    result = JSON.stringify(result)
                    result = result.replace(/\\n/g, "<br>").replace(/\\t/g, "&nbsp;&nbsp;&nbsp;&nbsp;")
                    $("#hoo-log-show").html(result);
                }else{
                    $("#hoo-log-show").html(result);
                }
            },
            error: function(xhr, status, error) {
                $("#hoo-log-show").html('加载失败！');
            }
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
