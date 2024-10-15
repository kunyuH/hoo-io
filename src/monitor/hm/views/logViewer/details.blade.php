
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
    <li class="list-group-item"><span class="api-log-field">input:</span><pre>{{$apiLog->input}}</pre></li>
    <li class="list-group-item"><span class="api-log-field">output:</span><pre>{{$apiLog->output}}</pre></li>
    <li class="list-group-item"><span class="api-log-field">status_code:</span> {{$apiLog->status_code}}</li>
    <li class="list-group-item"><span class="api-log-field">ip:</span> {{$apiLog->ip}}</li>
    <li class="list-group-item"><span class="api-log-field">created_at:</span> {{$apiLog->created_at}}</li>
    <li class="list-group-item">
        <span class="api-log-field">依赖服务:</span>
        <ul class="list-group mt-4 mb-4">
            @foreach($apiLog->HttpLog as $HttpLog)
                <li class="list-group-item">
                    <span class="api-log-field">url:</span> {{$HttpLog->url}}<br>
                    <span class="api-log-field">method:</span> {{$HttpLog->method}}<br>
                    <span class="api-log-field">path:</span> {{$HttpLog->path}}<br>
                    <span class="api-log-field">options:</span><pre>{{$HttpLog->options}}<pre>
                    <span class="api-log-field">response:</span><pre>{{$HttpLog->response}}</pre>
                    <span class="api-log-field">err:</span> {{$HttpLog->err}}<br>
                    <span class="api-log-field">run_time:</span> {{$HttpLog->run_time}}<br>
                    <span class="api-log-field">run_trace:</span> {{$HttpLog->run_trace}}<br>
                    <span class="api-log-field">run_path:</span> {{$HttpLog->run_path}}<br>
                    <span class="api-log-field">created_at:</span> {{$HttpLog->created_at}}<br>
                </li>
            @endforeach
        </ul>
</ul>
