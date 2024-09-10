<?php

namespace hoo\io\monitor\hm\Enums;

final class LogicalPipelinesArrangeEnums
{
    /**
     * 通用逻辑块
     */
    public const TYPE_COMMON = 'common';

    /**
     * 自定义逻辑块
     */
    public const TYPE_CUSTOM = 'custom';

    /**
     * 编排项类型
     */
    public const TYPE = [
        self::TYPE_COMMON,
        self::TYPE_CUSTOM,
    ];
}
