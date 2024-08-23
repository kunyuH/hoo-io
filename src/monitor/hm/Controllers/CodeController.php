<?php

namespace hoo\io\monitor\hm\Controllers;

use hoo\io\common\Request\HmIndexRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use hoo\io\common\Support\Facade\HooSession;


class CodeController extends BaseController
{
    /**
     * 首页
     * @return string
     */
    public function index()
    {
        return $this->v('code.index');
    }
    
    
}
