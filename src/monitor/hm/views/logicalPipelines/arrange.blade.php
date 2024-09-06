<div class="row">
    @foreach($pipelineData as $key=>$value)
        <div class="col-md-12">
            <div class="card">
                <div class="card-body" style="">
                    <h5 class="card-title">{{$value['block_name']}}</h5>
                    <p class="card-text">{{$value['block_remark']}}</p>
                    <form id="form-code-object-{{$value['id']}}">
                        <div class="form-group">
                            <input hidden name="id" value="{{$value['block_id']}}"/>
                            <input hidden name="name" value="{{$value['block_name']}}"/>
                            <input hidden name="group" value="{{$value['block_group']}}"/>
                            <input hidden name="label" value="{{$value['block_label']}}"/>
                            <input hidden name="remark" value="{{$value['block_remark']}}"/>
                            <textarea id="code-object-text-{{$value['id']}}" name="value" hidden>{{$value['block_logical_block']}}</textarea>
                            <pre id="code-object-edit-{{$value['id']}}"  data-id="{{$value['id']}}" class="logical-pipelines-arrange-ace-editor" style="min-height:200px;min-width: 400px"></pre>
                        </div>
                    </form>
                    <div class="float-left">
                        <a href="javascript:" class="btn btn-outline-primary btn-sm maxCode">最大化</a>
                    </div>
                    <div class="float-right">
                        <a href="javascript:" class="btn btn-outline-primary btn-sm RunLogicalPipeline">run</a>
                        <a href="javascript:"
                           type="button"
                           class="btn btn-outline-primary btn-sm ky-modal"
                           data-title="add previous"
                           data-width="800px"
                           data-height="600px"
                           data-href={{jump_link("/hm/logical-pipelines/add-arrange-item?pipeline_id={$value['logical_pipeline_id']}&arrange_id={$value['id']}&op=previous")}}
                        >add previous</a>
                        <a href="javascript:"
                           type="button"
                           class="btn btn-outline-primary btn-sm ky-modal"
                           data-title="add next"
                           data-width="800px"
                           data-height="600px"
                           data-href={{jump_link("/hm/logical-pipelines/add-arrange-item?pipeline_id={$value['logical_pipeline_id']}&arrange_id={$value['id']}&op=next")}}
                        >add next</a>
                        <a href="javascript:" class="btn btn-outline-primary btn-sm formRunCodeSave"
                           id="hm-code-object-save"
                           data-id="{{$value['id']}}"
                           data-href="{{jump_link('/hm/logical-block/save')}}"
                           data-from_id="form-code-object-{{$value['id']}}">save</a>
                        <a href="javascript:"
                           class="btn btn-danger btn-sm ky-req"
                           data-href="{{jump_link("/hm/logical-pipelines/delete-arrange?pipeline_id={$value['logical_pipeline_id']}&arrange_id={$value['id']}")}}"
                           data-type="POST"
                        >delete</a>
                    </div>
                </div>
            </div>
            <br>
        </div>
    @endforeach
</div>
