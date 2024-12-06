<?php

?>

<div style="    background-color: #141414;
    border-radius: .25rem;
    padding: 0 15px;color: #dddddd;">
    @foreach($list as $value)
        <div style="margin: 5px 0px;">{{$value}}</div>
    @endforeach
</div>
