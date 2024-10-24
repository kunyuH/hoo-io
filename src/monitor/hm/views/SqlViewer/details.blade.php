
<style>
    .log-field {
        font-weight: 500;
    }
</style>

<ul class="list-group mt-4 mb-4">
    <li class="list-group-item"><span class="log-field">id:</span> {{$logList->id}}</li>
    <li class="list-group-item"><span class="log-field">app_name:</span> {{$logList->app_name}}</li>
    <li class="list-group-item"><span class="log-field">hoo_traceid:</span> {{$logList->hoo_traceid}}</li>
    <li class="list-group-item"><span class="log-field">database:</span> {{$logList->database}}</li>
    <li class="list-group-item"><span class="log-field">connection_name:</span> {{$logList->connection_name}}</li>
    <li class="list-group-item"><span class="log-field">sql:</span><pre>{{$logList->sql}}</pre></li>
    <li class="list-group-item"><span class="log-field">run_time:</span> {{$logList->run_time}}</li>
    <li class="list-group-item"><span class="log-field">run_trace:</span> {{$logList->run_trace}}</li>
    <li class="list-group-item"><span class="log-field">run_path:</span> {{$logList->run_path}}</li>
    <li class="list-group-item"><span class="log-field">created_at:</span> {{$logList->created_at}}</li>
</ul>
