<?php

namespace hoo\io\common\Services;

use Rtgm\sm\RtSm4;

class CryptoService extends BaseService
{
    public static $key = 'bzlaxbrww2nkczqb';

    /**
     * sm4 加密
     * @param $data
     * @return string
     * @throws \Exception
     */
    public static function sm4Encrypt($data)
    {
        return (new RtSm4(self::$key))
            ->encrypt($data, 'sm4-ecb', '', 'base64');
    }

    /**
     * sm4 解密
     * @param $data
     * @return false|string
     * @throws \Exception
     */
    public static function sm4Decrypt($data)
    {
        return (new RtSm4(self::$key))
            ->decrypt($data, 'sm4-ecb', '', 'base64');
    }
}
