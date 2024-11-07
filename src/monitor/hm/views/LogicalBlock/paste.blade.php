<?php
$cdn = get_cdn().'/hm';
?>
<div class="row">
    <div class="col-12">
        <form id="hoo-modal-form">
            <div class="form-group">
                <br>
                <br>
                <textarea name="logical_block" class="form-control" style="height: 420px"></textarea>
            </div>
            <div class="float-right">
                <a href="javascript:"
                   class="btn btn-danger  modalClose"
                >Close</a>
                <a href="javascript:"
                   data-from_id="hoo-modal-form"
                   class="btn btn-primary formSubmit"
                   data-href="{{jump_link("/hm/logical-block/paste")}}"
                >Submit</a>
            </div>
        </form>
    </div>
</div>
