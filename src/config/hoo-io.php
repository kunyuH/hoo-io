<?php

return [

    'APP_ENV'=>env('APP_ENV', ''), //是否启用hoo.io

    #--------------------hoo_hm--------------------------
    'HOO_ENABLE'=>env('HOO_ENABLE', true), //是否启用hoo.io  默认启用
    'HOO_NAME'=>env('HOO_NAME'),
    'HOO_PASSWORD'=>env('HOO_PASSWORD'),
    'HOO_LOGIN_RETRY'=>env('HOO_LOGIN_RETRY',5), //登录重试次数 默认5次
    
    'SERVICE_NAME'=>env('SERVICE_NAME', ''),
    /**
     * 因为插件部分功能依赖CLOCKWORK插件 而页面访问链接需要资源前缀
     * 故也需要设置
     */
//    'ASSET_URL'
    #-------------------------------------------------------

    #--------------------hoo_hm api与依赖服务接口请求日志--------------------------
    'HM_API_LOG'=>env('HM_API_LOG', true),
    'HM_API_LOG_USER_FILED'=>env('HM_API_LOG_USER_FILED', 'member_id'),

    'HM_HTTP_LOG'=>env('HM_HTTP_LOG', true),

    'HM_SQL_LOG'=>env('HM_SQL_LOG', true),

    # 长度限制
    'HM_API_HTTP_LOG_LENGTH'=>env('HM_API_HTTP_LOG_LENGTH', 5000),

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
