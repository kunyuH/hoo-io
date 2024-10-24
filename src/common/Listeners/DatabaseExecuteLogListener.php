<?php

namespace hoo\io\common\Listeners;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;

final class DatabaseExecuteLogListener
{
    /**
     * 处理模式:常规
     */
    protected const MODEL__NORMAL = 'normal';

    /**
     * 处理模式:解析
     */
    protected const MODEL__RESOLVE = 'resolve';

    /**
     * 输出描述信息状态
     * PS: 针对每次请求，仅输出一次描述信息
     *
     * @var bool
     */
    protected static  $outputDescription = false;

    /**
     * 针对此次请求的UUID
     *
     * @var bool
     */
    protected static  $requestUuid = '';

    /**
     * 日志通知
     *
     * @var ?LoggerInterface
     */
    protected static $logChannel = null;

    /**
     * @param QueryExecuted $query
     * @param string $mode 处理模式(默认情况下使用解析模式，当无效时回退到常规模式)
     * @return void
     */
    public function handle(QueryExecuted $query, string $mode = self::MODEL__RESOLVE): void
    {

    }
}
