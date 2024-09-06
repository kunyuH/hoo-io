<?php

namespace hoo\io\common\Exceptions;

use hoo\io\common\Enums\HttpResponseEnum;
use Illuminate\Http\JsonResponse;

/**
 * 异常
 */
class HooException extends \Exception implements \Throwable
{
    public function render($request)
    {
        return new JsonResponse([
            'message' => $this->getMessage(),
            'code' => $this->getCode()==0?HttpResponseEnum::BUSINESS_ERROR_412:$this->getCode(),
            'data' => [],
        ]);
    }
}
