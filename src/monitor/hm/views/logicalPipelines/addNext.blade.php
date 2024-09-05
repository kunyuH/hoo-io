<?php
$cdn = get_cdn().'/hm';
?>
<script src="<?php echo $cdn?>/ace-builds-master/src/ace.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo $cdn?>/ace-builds-master/src/ext-language_tools.js" type="text/javascript" charset="utf-8"></script>

<div class="row">
    <div class="col-12 mb-3 mt-3">
        <form id="logical-pipeline-add-next">
            <input hidden name="id" value="{{$id??''}}">
            <input hidden name="arrange_id" value="{{$arrange_id??''}}">
            <div class="form-group">
                <label for="logical-block-select" class="star">logical block</label>
                <select class="form-control" name="logical_block_id" id="logical-block-select">
                    <option value="" selected>logical block...</option>
                    @foreach($logical_blocks as $logical_block)
                    <option value="{{$logical_block->id}}">{{$logical_block->name}}【{{$logical_block->group}}】</option>
                    @endforeach
                </select>
            </div>
            <div class="float-right">
                <a href="javascript:"
                   data-from_id="logical-pipeline-add-next"
                   class="btn btn-primary formSubmit"
                   data-href={{jump_link('/hm/logical-pipelines/add-next')}}
                >Submit</a>
                <a href="javascript:"
                   class="btn btn-danger  modalClose"
                >Close</a>
            </div>
        </form>
    </div>
</div>
