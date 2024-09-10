<?php

namespace hoo\io\common\Support\Facade;

use Illuminate\Support\Facades\Facade;
use \hoo\io\common\Services\HooSessionService;

/**
 * 逻辑线模块
 *
 * 设置hoo session id
 * @method static setId()
 * @var HooSessionService::setId()
 *
 * 获取hoo session id
 * @method static getId()
 * @var HooSessionService::getId()
 *
 * 获取hoo session 内key的值
 * @method static get($key)
 * @var HooSessionService::get($key)
 *
 * 设置hoo session 内key的值
 * @method static put($key, $value)
 * @var HooSessionService::put()
 *
 * 获取hoo session内所有数据
 * @method static all()
 * @var HooSessionService::all()
 *
 * 删除hoo session内key的值
 * @method static remove($key)
 * @var HooSessionService::remove()
 *
 *
 * @see HooSessionService
 */
class HooSession extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return HooSessionService::class;
    }
}
