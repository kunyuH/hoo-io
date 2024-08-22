<div class="row">
    <div class="col-12">
        <h1>Home</h1>
        <p>Welcome to the Laravel.io Monitor.</p>
        <p>Here you can see the status of the Laravel.io API.</p>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <a href="javascript:"
                   type="button"
                   class="btn btn-outline-primary ky-req"
                   data-type="post"
                   data-confirm-ky="确认要运行命令吗？"
                   data-params='{"value":"log-viewer:publish"}'
                   data-href={{jump_link("/hm/run-command")}}
                >log-viewer初始化</a>
            </div>
        </div>
    </div>
</div>
<div class="row mt-3">
    <div class="col-12 d-flex">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">运行命令</h5>
                <h6 class="card-subtitle mb-2 text-muted">run command</h6>
                <p class="card-text">可运行程序内Commands脚本</p>
                <a href="javascript:"
                   type="button"
                   class="btn btn-dark ky-modal"
                   data-title="run command"
                   data-width="800px"
                   data-height="480px"
                   data-href={{jump_link("/hm/run-command?submitTo=/hm/run-command")}}
                >run command</a>
            </div>
        </div>
        <div class="card ml-3">
            <div class="card-body">
                <h5 class="card-title">运行php脚本</h5>
                <h6 class="card-subtitle mb-2 text-muted">run code</h6>
                <p class="card-text">可直接运行php代码，支持class，引入</p>
                <a href="javascript:"
                   type="button"
                   class="btn btn-dark ky-modal"
                   data-title="run code"
                   data-width="800px"
                   data-height="480px"
                   data-href={{jump_link("/hm/run-code?submitTo=/hm/run-code")}}
                >run code</a>
            </div>
        </div>
    </div>
</div>
