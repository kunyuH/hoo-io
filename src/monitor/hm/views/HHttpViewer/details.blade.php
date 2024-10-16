
<style>
    .hhttp-log-field {
        font-weight: 500;
    }
</style>

<ul class="list-group mt-4 mb-4">
    <li class="list-group-item"><span class="hhttp-log-field">id:</span> {{$hHttpLogList->id}}</li>
    <li class="list-group-item"><span class="hhttp-log-field">app_name:</span> {{$hHttpLogList->app_name}}</li>
    <li class="list-group-item"><span class="hhttp-log-field">hoo_traceid:</span> {{$hHttpLogList->hoo_traceid}}</li>
    <li class="list-group-item"><span class="hhttp-log-field">path:</span> {{$hHttpLogList->path}}</li>
    <li class="list-group-item"><span class="hhttp-log-field">method:</span> {{$hHttpLogList->method}}</li>
    <li class="list-group-item"><span class="hhttp-log-field">options:</span><pre>{{$hHttpLogList->options}}</pre></li>
    <li class="list-group-item"><span class="hhttp-log-field">response:</span><pre>{{$hHttpLogList->response}}</pre></li>
    <li class="list-group-item"><span class="hhttp-log-field">err:</span> {{$hHttpLogList->err}}</li>
    <li class="list-group-item"><span class="hhttp-log-field">url:</span> {{$hHttpLogList->url}}</li>
    <li class="list-group-item"><span class="hhttp-log-field">run_time:</span> {{$hHttpLogList->run_time}}</li>
    <li class="list-group-item"><span class="hhttp-log-field">run_trace:</span> {{$hHttpLogList->run_trace}}</li>
    <li class="list-group-item"><span class="hhttp-log-field">run_path:</span> {{$hHttpLogList->run_path}}</li>
    <li class="list-group-item"><span class="hhttp-log-field">created_at:</span> {{$hHttpLogList->created_at}}</li>
</ul>
