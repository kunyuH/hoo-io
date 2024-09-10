<?php
$cdn = get_cdn().'/hm';
?>
<script src="<?php echo $cdn?>/ace-builds-master/src/ace.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo $cdn?>/ace-builds-master/src/ext-language_tools.js" type="text/javascript" charset="utf-8"></script>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="float-right mb-3">
                    <a href="javascript:"
                       type="button"
                       class="btn btn-outline-primary btn-sm ky-modal"
                       data-title="create"
                       data-width="800px"
                       data-height="600px"
                       data-href={{jump_link("/hm/logical-pipelines/save")}}
                    >create</a>
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
                    @foreach($logicalPipelines as $pipeline)
                        <tr>
                            <th scope="row">{{$pipeline->id}}</th>
                            <td>{{$pipeline->group}}</td>
                            <td>{{$pipeline->rec_subject_id}}</td>
                            <td>{{$pipeline->name}}</td>
                            <td>{{$pipeline->label}}</td>
                            <td><a href="javascript:"
                                   class="btn btn-outline-primary btn-sm RunLogicalPipeline"
                                   data-href={{jump_link('/hm/logical-pipelines/run?id='.$pipeline->id)}}
                                >Run</a>
                                <a href="javascript:"
                                   type="button"
                                   class="btn btn-outline-primary btn-sm ky-modal"
                                   data-title="edit"
                                   data-width="800px"
                                   data-height="600px"
                                   data-href={{jump_link("/hm/logical-pipelines/save?id=".$pipeline->id)}}
                                >edit</a>
                                <a href="javascript:"
                                   type="button"
                                   class="btn btn-outline-primary btn-sm logical-pipelines-arrange"
                                   data-id="{{$pipeline->id}}"
                                >arrange</a>
                                <a href="javascript:"
                                   class="btn btn-danger btn-sm ky-req"
                                   data-type="POST"
                                   data-confirm-ky="确定删除【{{$pipeline->name}}】么？"
                                   data-href={{jump_link('/hm/logical-pipelines/delete?id='.$pipeline->id)}}
                                >delete</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
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
<style>

</style>
{{--<script src="<?php echo jump_link('/hm-r/js/logicalPipelines/main.js')?>" type="text/javascript" charset="utf-8"></script>--}}
<script src="<?php echo $cdn?>/js/logicalPipelines/main.js" type="text/javascript" charset="utf-8"></script>


