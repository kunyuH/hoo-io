
<style>
    .api-log-field {
        font-weight: 500;
    }
</style>

<ul class="list-group mt-4 mb-4">
    <li class="list-group-item"><span class="api-log-field">id:</span> {{$apiLog->id}}</li>
    <li class="list-group-item"><span class="api-log-field">app_name:</span> {{$apiLog->app_name}}</li>
    <li class="list-group-item"><span class="api-log-field">hoo_traceid:</span> {{$apiLog->hoo_traceid}}</li>
    <li class="list-group-item"><span class="api-log-field">user_id:</span> {{$apiLog->user_id}}</li>
    <li class="list-group-item"><span class="api-log-field">domain:</span> {{$apiLog->domain}}</li>
    <li class="list-group-item"><span class="api-log-field">method:</span> {{$apiLog->method}}</li>
    <li class="list-group-item"><span class="api-log-field">path:</span> {{$apiLog->path}}</li>
    <li class="list-group-item"><span class="api-log-field">run_time:</span> {{$apiLog->run_time}}</li>
    <li class="list-group-item"><span class="api-log-field">user_agent:</span> {{$apiLog->user_agent}}</li>
    <li class="list-group-item"><span class="api-log-field">input:</span><pre class="json-show">{{$apiLog->input}}</pre></li>
    <li class="list-group-item"><span class="api-log-field">output:</span><pre class="json-show">{{$apiLog->output}}</pre></li>
    <li class="list-group-item"><span class="api-log-field">status_code:</span> {{$apiLog->status_code}}</li>
    <li class="list-group-item"><span class="api-log-field">ip:</span> {{$apiLog->ip}}</li>
    <li class="list-group-item"><span class="api-log-field">created_at:</span> {{$apiLog->created_at}}</li>
    <li class="list-group-item">
        <span class="api-log-field">依赖HTTP服务:</span>
        <ul class="list-group mt-4 mb-4">
            @foreach($apiLog->HttpLog as $HttpLog)
                <li class="list-group-item">
                    <span class="api-log-field">path:</span> {{$HttpLog->path}}<br>
                    <span class="api-log-field">method:</span> {{$HttpLog->method}}<br>
                    <span class="api-log-field">options:</span><pre class="json-show">{{$HttpLog->options}}</pre>
                    <span class="api-log-field">response:</span><pre class="json-show">{{$HttpLog->response}}</pre>
                    <span class="api-log-field">err:</span> {{$HttpLog->err}}<br>
                    <span class="api-log-field">url:</span> {{$HttpLog->url}}<br>
                    <span class="api-log-field">run_time:</span> {{$HttpLog->run_time}}<br>
                    <span class="api-log-field">run_trace:</span> {{$HttpLog->run_trace}}<br>
                    <span class="api-log-field">run_path:</span> {{$HttpLog->run_path}}<br>
                    <span class="api-log-field">created_at:</span> {{$HttpLog->created_at}}<br>
                    <span class="api-log-field">http_id:</span> {{$HttpLog->id}}<br>
                </li>
            @endforeach
        </ul>
        <span class="api-log-field">依赖DATABASE服务:</span>
        <ul class="list-group mt-4 mb-4">
            @foreach($apiLog->SqlLog as $SqlLog)
                <li class="list-group-item">
                    <span class="api-log-field">sql:</span><pre>{{$SqlLog->sql}}</pre>
                    <span class="api-log-field">database:</span> {{$SqlLog->database}}<br>
                    <span class="api-log-field">connection_name:</span> {{$SqlLog->connection_name}}<br>
                    <span class="api-log-field">run_time:</span> {{$SqlLog->run_time}}<br>
                    <span class="api-log-field">run_trace:</span> {{$SqlLog->run_trace}}<br>
                    <span class="api-log-field">run_path:</span> {{$SqlLog->run_path}}<br>
                    <span class="api-log-field">created_at:</span> {{$SqlLog->created_at}}<br>
                    <span class="api-log-field">sql_id:</span> {{$SqlLog->id}}<br>
                </li>
            @endforeach
        </ul>
    </li>
</ul>
