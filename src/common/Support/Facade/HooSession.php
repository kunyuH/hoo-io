<?php

namespace hoo\io\common\Support\Facade;

use Illuminate\Support\Facades\Facade;
use \hoo\io\common\Services\HooSessionService;

class HooSession extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return HooSessionService::class;
    }
}
