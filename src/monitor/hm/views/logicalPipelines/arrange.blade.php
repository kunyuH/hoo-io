<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body" style="">
                <h5 class="card-title">【{{$pipeline->name}}】逻辑流程图</h5>
                <p style="margin-bottom: 0">根据逻辑调整流程</p>
                <p style="margin-bottom: 0">逻辑线访问方式</p>
                <p style="margin-bottom: 0">api：{{$pipeline->group}}/{{$pipeline->rec_subject_id}}</p>
                <p style="margin-bottom: 0">method：{{$pipeline->setting['method']}}</p>
                @if(empty($arranges))
                    <a href="javascript:"
                       type="button"
                       class="btn btn-outline-primary btn-sm ky-modal"
                       data-title="add next"
                       data-width="800px"
                       data-height="600px"
                       data-href={{jump_link("/hm/logical-pipelines/add-arrange-item?pipeline_id={$pipeline->id}&arrange_id=0&op=next")}}
                    >add next</a>
                @endif
            </div>
        </div>
        <br>
    </div>
    @foreach($arranges as $key=>$value)
        <div class="col-md-12">
            <div class="card">
                <div class="card-body dropdown" style="padding-bottom: 0;">
                    <h5 class="card-title float-left">{{$value['block_name']}}</h5>
                    <span class="float-right" style="padding: 0px 10px;">
                        <i class="bi bi-arrows-fullscreen maxCode" data-id="{{$value['id']}}"></i>
                    </span>
                    <span class="float-right" data-toggle="dropdown" aria-expanded="false" style="padding: 0px 10px;">
                        <i class="bi bi-three-dots-vertical"></i>
                    </span>
                    <div class="dropdown-menu" style="margin-left:-110px">
                        <a href="javascript:"
                           data-from_id="hoo-run-code"
                           class="dropdown-item formRunCodeSubmit"
                           data-href={{jump_link('/hm/logical-block/run')}}
                        >Run</a>
                        <div class="dropdown-divider"></div>
                        <a href="javascript:"
                           type="button"
                           class="dropdown-item ky-modal"
                           data-title="add previous"
                           data-width="800px"
                           data-height="600px"
                           data-href={{jump_link("/hm/logical-pipelines/add-arrange-item?pipeline_id={$value['logical_pipeline_id']}&arrange_id={$value['id']}&op=previous")}}
                        >add previous</a>
                        <a href="javascript:"
                           type="button"
                           class="dropdown-item ky-modal"
                           data-title="add next"
                           data-width="800px"
                           data-height="600px"
                           data-href={{jump_link("/hm/logical-pipelines/add-arrange-item?pipeline_id={$value['logical_pipeline_id']}&arrange_id={$value['id']}&op=next")}}
                        >add next</a>
                        <div class="dropdown-divider"></div>
                        <a href="javascript:" class="dropdown-item formRunCodeSave"
                           id="hm-code-object-save"
                           data-id="{{$value['id']}}"
                           data-href="{{jump_link('/hm/logical-block/save')}}"
                           data-from_id="form-code-object-{{$value['id']}}">save</a>
                        <div class="dropdown-divider"></div>
                        <a href="javascript:"
                           class="dropdown-item ky-req"
                           data-confirm-ky="Are you sure?"
                           data-href="{{jump_link("/hm/logical-pipelines/delete-arrange?pipeline_id={$value['logical_pipeline_id']}&arrange_id={$value['id']}")}}"
                           data-type="POST"
                        >delete</a>
                    </div>

                    <p class="card-text">{{$value['block_remark']}}</p>
                    <form id="form-code-object-{{$value['id']}}">
                        <div class="form-group">
                            <input hidden name="id" value="{{$value['block_id']}}"/>
                            <input hidden name="object_id" value="{{$value['block_object_id']}}"/>
                            <input hidden name="name" value="{{$value['block_name']}}"/>
                            <input hidden name="group" value="{{$value['block_group']}}"/>
                            <input hidden name="label" value="{{$value['block_label']}}"/>
                            <input hidden name="remark" value="{{$value['block_remark']}}"/>
                            <textarea id="code-object-text-{{$value['id']}}" name="logical_block" hidden>{{$value['block_logical_block']}}</textarea>
                            <pre id="code-object-edit-{{$value['id']}}"  data-id="{{$value['id']}}" class="logical-pipelines-arrange-ace-editor" style="min-height:200px;min-width: 400px"></pre>
                        </div>
                    </form>
                </div>
            </div>
            <br>
        </div>
    @endforeach
</div>
