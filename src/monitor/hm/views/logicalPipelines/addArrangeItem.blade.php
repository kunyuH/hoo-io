<?php
$cdn = get_cdn().'/hm';
?>
<script src="<?php echo $cdn?>/ace-builds-master/src/ace.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo $cdn?>/ace-builds-master/src/ext-language_tools.js" type="text/javascript" charset="utf-8"></script>

<div class="row">
    <div class="col-12 mb-3 mt-3">
        <form id="logical-pipeline-add-next">
            <input hidden name="pipeline_id" value="{{$pipeline_id??''}}">
            <input hidden name="op" value="{{$op??''}}">
            <input hidden name="arrange_id" value="{{$arrange_id??''}}">
            <div class="form-group">
                <label for="type-select" class="star">type</label>
                <select class="form-control" name="type" id="type-select">
                    <option value="common" selected>common</option>
                    <option value="custom" >custom</option>
                </select>
            </div>
            <div class="form-group" id="logical-block-id-select">
                <label for="logical-block-select" class="star">logical block</label>
                <select class="form-control" name="logical_block_id">
                    <option value="" selected>logical block...</option>
                    @foreach($logical_blocks as $logical_block)
                    <option value="{{$logical_block->id}}">{{$logical_block->name}}【{{$logical_block->group}}】</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" id="logical-block-name">
                <label class="star">name</label>
                <input type="text" class="form-control" name="name">
            </div>
            <div class="form-group" id="logical-block">
                <label class="star">logical block</label>
                <textarea id="add-arrange-item-logical-block-txt" name="logical_block" hidden></textarea>
                <pre id="add-arrange-item-logical-block" style="min-height:200px;"></pre>
            </div>
            <div class="float-right">
                <a href="javascript:"
                   class="btn btn-danger  modalClose"
                >Close</a>
                <a href="javascript:"
                   data-from_id="logical-pipeline-add-next"
                   class="btn btn-primary formSubmitAddArrangeItem"
                   data-href={{jump_link('/hm/logical-pipelines/add-arrange-item')}}
                >Submit</a>
            </div>
        </form>
    </div>
</div>
<style>
    #logical-block{
        display:none;
    }
    #logical-block-name{
        display:none;
    }
</style>
<script>
    /**
     * 页面加载后运行
     */
    $(document).ready(function(){
        edit_index = 'add-arrange-item-logical-block';
        edit_init(edit_index,edit_index)
        editorSetDifaultCode(edit_index)

        changeTypeShow($('#type-select'))
    })

    /**
     * type选项触发
     */
    $('#type-select').change(function(){
        changeTypeShow(this)
    });

    $(document).on("click",".formSubmitAddArrangeItem",function(){
        var from_id = $(this).attr('data-from_id');
        var url = $(this).attr('data-href');

        $("#add-arrange-item-logical-block-txt").val(editors['add-arrange-item-logical-block'].getValue());

        layer.load(1); //loading
        $("#"+from_id).ajaxSubmit({
            type:"post",
            url:url,
            dataType: 'json' ,
            success: function (result) {
                layer.closeAll('loading'); //关闭loading
                if(result.code == 200){
                    parent.layer.close(parent.layer.getFrameIndex(window.name));
                    layer.msg(result.message, {icon: 16,shade: 0.01,time:"-1"});
                    window.parent.location.href = result.data.redirect_uri;
                }else{
                    layer.alert(result.message, {icon: 5});
                }
            }
        });
    })

    /**
     * type选项展示特定输入框
     * @param _this
     */
    function changeTypeShow(_this)
    {
        if($(_this).val() == 'custom'){
            $('#logical-block-id-select').hide();
            $('#logical-block').show();
            $('#logical-block-name').show();
        }else{
            $('#logical-block-id-select').show();
            $('#logical-block').hide();
            $('#logical-block-name').hide();
        }
    }
</script>
