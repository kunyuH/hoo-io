<?php

namespace hoo\io\monitor\hm\Controllers;

use hoo\io\common\Enums\SessionEnum;
use hoo\io\monitor\hm\Web;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use hoo\io\common\Support\Facade\HooSession;


class IndexController extends BaseController
{
    /**
     * 首页
     * @return string
     */
    public function index()
    {
        return $this->v('index.index',[
            'name' => HooSession::get(SessionEnum::USER_INFO_KEY)['name']??null,
            'hooAuth'=>Gate::allows('hooAuth')?1:0
        ]);
    }
}
