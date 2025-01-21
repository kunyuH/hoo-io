<?php

return [

    'APP_ENV'=>env('APP_ENV', ''), //是否启用hoo.io

    #--------------------hoo_hm--------------------------
    'HOO_ENABLE'=>env('HOO_ENABLE', true), //是否启用hoo.io  默认启用
    'IS_LOGIN'=>env('IS_LOGIN', true),      //是否需要账密登录  默认需要
    'HOO_NAME'=>env('HOO_NAME'),
    'HOO_PASSWORD'=>env('HOO_PASSWORD'),
    'HOO_LOGIN_RETRY'=>env('HOO_LOGIN_RETRY',5), //登录重试次数 默认5次

    'SERVICE_NAME'=>env('SERVICE_NAME', ''),    //服务前缀

    'HOO_DATABASE_DEFAULT'=>env('HOO_DATABASE_DEFAULT', null),    //服务前缀


    /**
     * 因为插件部分功能依赖CLOCKWORK插件 而页面访问链接需要资源前缀
     * 故也需要设置
     */
    //'ASSET_URL'
    #-------------------------------------------------------

    #--------------------hoo_hm api与依赖服务接口请求日志【api hhttp sql】--------------------------
    # api日志开关 是否记录
    'HM_API_LOG'=>env('HM_API_LOG', true),
    # 默认api日志中user_id 提取 request 对象内的属性
    'HM_API_LOG_USER_FILED'=>env('HM_API_LOG_USER_FILED', 'member_id'),
    # 允许不记录日志的路由
    'HM_API_LOG_NOT_ROUTE'=>env('HM_API_LOG_NOT_ROUTE',''),


    # hhttp日志开关 是否记录
    'HM_HTTP_LOG'=>env('HM_HTTP_LOG', true),
    # hhttp日志开关 跑命令时的hhttp日志是否记录
    'HM_COMMAND_HTTP_LOG'=>env('HM_COMMAND_HTTP_LOG', false),

    # sql日志开关 是否记录
    'HM_SQL_LOG'=>env('HM_SQL_LOG', true),
    # sql日志开关 跑命令时的sql是否记录
    'HM_SQL_COMMAND_LOG'=>env('HM_SQL_COMMAND_LOG', false),

    # 长度限制
    'HM_API_HTTP_LOG_LENGTH'=>env('HM_API_HTTP_LOG_LENGTH', 5000),

    # 日志清理设置
    # api日志清理多久之前的日志 默认 60天前的
    'HM_API_LOG_CLEAN'=>env('HM_API_LOG_CLEAN', 60),
    # hhttp日志清理多久之前的日志 默认 60天前的
    'HM_HPPT_LOG_CLEAN'=>env('HM_HPPT_LOG_CLEAN', 60),
    # sql日志清理多久之前的日志 默认 60天前的
    'HM_SQL_LOG_CLEAN'=>env('HM_SQL_LOG_CLEAN', 60),
    #-------------------------------------------------------


    /**
     * 以下配置也需要在env中设置
     * 因为插件部分功能依赖CLOCKWORK插件 所以CLOCKWORK插件配置也需要设置
     */
//    'CLOCKWORK_ENABLE'
//    'CLOCKWORK_STORAGE_EXPIRATION'
//    'CLOCKWORK_AUTHENTICATION'
//    'CLOCKWORK_AUTHENTICATION_PASSWORD'
];
