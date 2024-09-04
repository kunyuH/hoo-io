<?php
$cdn = get_cdn().'/hm';
?>
<script src="<?php echo $cdn?>/ace-builds-master/src/ace.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo $cdn?>/ace-builds-master/src/ext-language_tools.js" type="text/javascript" charset="utf-8"></script>

<div class="row">
    <div class="col-12 mb-3 mt-3">
        <form id="logical-pipeline-create">
            <input hidden name="id" value="{{$id??''}}">
            <div class="form-group">
                <label class="star">route</label>
                <input type="text" class="form-control" name="route" value="{{$route??''}}" placeholder="demo/test">
            </div>
            <div class="form-group">
                <label class="star">name</label>
                <input type="text" class="form-control" value="{{$name??''}}" name="name">
            </div>
            <div class="form-group">
                <label class="star">group</label>
                <input type="text" class="form-control" value="{{$group??''}}" name="group">
            </div>
            <div class="form-group">
                <label>label</label>
                <input type="text" class="form-control" value="{{$label??''}}" name="label">
            </div>
            <div class="form-group">
                <label>remark</label>
                <textarea class="form-control" name="remark" value="{{$remark??''}}" rows="3"></textarea>
            </div>
            <div class="float-right">
                <a href="javascript:"
                   data-from_id="logical-pipeline-create"
                   class="btn btn-primary formSubmit"
                   data-href={{jump_link('/hm/logical-pipelines/save')}}
                >Submit</a>
                <a href="javascript:"
                   class="btn btn-danger  modalClose"
                >Close</a>
            </div>
        </form>
    </div>
</div>
<style>

</style>
<script>

</script>
