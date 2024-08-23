<?php
namespace hoo\io\common\Services;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use \Illuminate\Support\Facades\Cache;

class HooSessionService extends BaseService
{
    public $id;

    public $cache_key_prefix = 'hoo-session-';

    # 生命周期
    public $expires = 60 * 60 * 24 * 1; // 1天

    public function __construct()
    {
        # 获取cookie内的session
        $session_id = Request::cookie('x-hoo-session-id');
        if (!$session_id) {
            $name = 'x-hoo-session-id';
            $session_id = Session::getId();
            $expires = time() + 60 * 60 * 24 * 30;
            setcookie($name, $session_id, $expires,'/'.env('SERVICE_NAME'));
        }
        $this->id = $session_id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId(){
        return $this->id;
    }

    public function get($key)
    {
        return Cache::get($this->getCacheKey())[$key]??null;
    }

    public function put($key, $value)
    {
        return Cache::put($this->getCacheKey(), array_merge(Cache::get($this->getCacheKey())??[], [$key => $value]), $this->expires);
    }

    public function all()
    {
        return Cache::get($this->getCacheKey());
    }

    public function remove($key)
    {
        $data = Cache::get($this->getCacheKey());
        unset($data[$key]);
        return Cache::put($this->getCacheKey(), $data, $this->expires);
    }

    private function getCacheKey()
    {
        return $this->cache_key_prefix.$this->id;
    }
}
