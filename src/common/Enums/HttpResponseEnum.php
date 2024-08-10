<?php

namespace hoo\io\common\Enums;

/**
 * http响应码
 */
final class HttpResponseEnum
{
    # code成功
    public const SUCCESS = 200;

    # code token有误
    public const UNAUTHORIZED = 403;

    # code失败
    public const ERROR = 400;

    // 业务错误412
    public const BUSINESS_ERROR_412 = 412;

    // 业务错误422
    public const BUSINESS_ERROR_422 = 422;


}
