<div class="row">
    <div class="col-8">
        <div class="card">
            <div class="card-body">
                <form id="hoo-run-codex">
                    <div class="form-group">
                        <label for="exampleFormControlTextarea1">Example textarea</label>
                        <textarea class="form-control" name="value" style="height: 500px" rows="3"></textarea>
                    </div>
                    <a href="javascript:"
                       data-from_id="hoo-run-codex"
                       class="btn btn-primary formRunCodeSubmit"
                       data-href={{jump_link('/hm/run-code')}}
                    >Submit</a>
                    <a href="javascript:"
                       data-from_id="hoo-run-codex"
                       class="btn btn-primary formSaveCodeSubmit"
                       data-href={{jump_link('/hm/run-code')}}
                    >Save</a>
                </form>
            </div>
        </div>
    </div>
    <div class="col-4">
        <div class="card">
            <div class="card-body">
                <div class="btn-group">
                    <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        Action
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#">Action</a>
                        <a class="dropdown-item" href="#">Another action</a>
                        <a class="dropdown-item" href="#">Something else here</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">Separated link</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 mt-3">
        <div class="card">
            <div class="card-body">
                <pre id="run-code-output">

                </pre>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).on("click",".formRunCodeSubmit",function(){

        var from_id = $(this).attr('data-from_id');
        var url = $(this).attr('data-href');

        layer.load(1); //loading
        $("#"+from_id).ajaxSubmit({
            type:"post",
            url:url,
            dataType: 'json' ,
            success: function (result) {
                layer.closeAll('loading'); //关闭loading
                if(result.code == 200){
                    console.log(11)
                    $("#run-code-output").html(result.message);
                }else{
                    layer.alert(result.message, {icon: 5});
                }
            }
        });
    })
</script>
