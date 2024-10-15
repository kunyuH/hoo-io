<?php
$cdn = get_cdn().'/hm';
?>
<script src="<?php echo $cdn?>/ace-builds-master/src/ace.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo $cdn?>/ace-builds-master/src/ext-language_tools.js" type="text/javascript" charset="utf-8"></script>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form id="form-logical-pipelines-search">
                    <div class="form-group">
                        <div class="row">
                            <div class="col">
                                <input type="text" name="group" placeholder="group" class="form-control">
                            </div>
                            <div class="col">
                                <input type="text" name="rec_subject_id" placeholder="rec_subject_id" class="form-control">
                            </div>
                            <div class="col">
                                <input type="text" name="name" placeholder="name" class="form-control">
                            </div>
                            <div class="col">
                                <input type="text" name="label" placeholder="label" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="float-right">
                        <a href="javascript:"
                           class="btn btn-link btn-sm"
                           onclick="resetForm('#form-logical-pipelines-search')"
                        >重置</a>
                        <a href="javascript:"
                           class="btn btn-link btn-sm logical-pipelines-search"
                        >查询</a>
                    </div>
                </form>
                <div class="float-left mb-3">
                    <a href="javascript:"
                       type="button"
                       class="btn btn-outline-primary btn-sm ky-modal"
                       data-title="create"
                       data-width="800px"
                       data-height="600px"
                       data-href={{jump_link("/hm/logical-pipelines/save")}}
                    >create</a>
                    <a target="_blank" href='{{jump_link("/hm/logical-pipelines/arrangex")}}' type="button" class="btn btn-link btn-sm">arrange</a>
                </div>
                <table class="table table-sm table-striped">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">group</th>
                        <th scope="col">rec_subject_id</th>
                        <th scope="col">name</th>
                        <th scope="col">label</th>
                        <th scope="col">op</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($logicalPipelines as $key=>$pipeline)
                        <tr>
                            <th scope="row">{{$key+1}}</th>
                            <td>{{$pipeline->group}}</td>
                            <td>{{$pipeline->rec_subject_id}}</td>
                            <td>{{$pipeline->name}}</td>
                            <td>{{$pipeline->label}}</td>
                            <td><a href="javascript:"
                                   class="btn btn-link btn-sm RunLogicalPipeline"
                                   data-href={{jump_link('/hm/logical-pipelines/run?id='.$pipeline->id)}}
                                >run</a>
                                <a href="javascript:"
                                   type="button"
                                   class="btn btn-link btn-sm ky-modal"
                                   data-title="edit"
                                   data-width="800px"
                                   data-height="600px"
                                   data-href={{jump_link("/hm/logical-pipelines/save?id=".$pipeline->id)}}
                                >edit</a>
                                <a href="javascript:"
                                   type="button"
                                   class="btn btn-link btn-sm logical-pipelines-arrange"
                                   data-id="{{$pipeline->id}}"
                                >arrange</a>
                                <a href="javascript:"
                                   class="btn btn-link btn-sm ky-req"
                                   data-type="POST"
                                   data-confirm-ky="确定删除【{{$pipeline->name}}】么？"
                                   data-href={{jump_link('/hm/logical-pipelines/delete?id='.$pipeline->id)}}
                                >delete</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="float-right">
                    {{$logicalPipelines->appends(request()->query())->links('pagination::bootstrap-4')}}
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 mt-3">
        <div class="accordion" id="accordionExample">
            <div class="card">
                <div class="card-header card-sm" id="headingOne">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            pipeline
                        </button>
                    </h2>
                </div>

                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                    <div class="card-body" id="logical-pipelines-arrange-show">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 mt-3">
        <div class="card">
            <div class="card-body">
                <pre id="run-code-output" style="min-height: 500px">
                </pre>
            </div>
        </div>
    </div>
</div>
{{-- <script src="{{jump_link('/hm-r/js/logicalPipelines/main.js')}}" type="text/javascript" charset="utf-8"></script>--}}
<script src="{{$cdn}}/js/logicalPipelines/main.js" type="text/javascript" charset="utf-8"></script>
