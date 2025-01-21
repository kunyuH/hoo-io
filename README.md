# laravel-io

### 简介
各类io扩展
- 数据库
- http
- 日志记录
- 逻辑单元

### 安装:
```bash
composer require hoo/laravel-io
```

### 日志配置
filesystems.php 增加配置:
```php
'debug'  => [
    'driver' => 'daily',
    'path'   => storage_path('logs/io/laravel.log'),
    'level'  => 'debug',
    'days'   => 30, # 保留30天 根据具体情况设置
],
```

### http客户端调用(与GuzzleHttp用法一致；增加了请求日志记录)
```php
$uri = config('http_service.inner_service') . '/api/test';
$res = (new HHttp())->post(
    uri: $uri,
    options: [
        'form_params' => $requestData
    ]
);
$data = $res->getBody()->getContents()
#----------------------------------------------------------------
$uri = config('http_service.inner_service') . '/api/test';
$res = (new HHttp())->post(
    uri: $uri,
    options: [
        'headers' => [
            'Content-Type' => 'application/json'
        ],
        'json' => [
            'card_no' => $account_id,
        ],
    ]
);
$data = $res->getBody()->getContents()
```

### CLOCKWORK监控模块
- env配置
```bash
#--------------------CLOCKWORK--------------------------
# 限制环境 local 开发环境可进 test 测试环境可进 production 生产环境 且请求头中有灰度标识可进 其它环境不可进
# 是否开启CLOCKWORK服务
CLOCKWORK_ENABLE=true
# 收集的数据存储时效 单位：分钟
CLOCKWORK_STORAGE_EXPIRATION=120
# 【控制面板访问】是否开启第二层密码
CLOCKWORK_AUTHENTICATION=false
# 【控制面板访问】第二层密码（默认密码：VerySecretPassword）
CLOCKWORK_AUTHENTICATION_PASSWORD=
#-------------------------------------------------------
```
- 配置收集的数据清理脚本
- \App\Console\Kernel::schedule方法中增加
```php
    # 应用hoo自定义的定时
    (new \hoo\io\common\Console\Kernel())->schedule($schedule);
```

### HM监控模块
- env配置
```bash
#--------------------hoo_hm--------------------------
HOO_ENABLE=true
# 是否需要登录 默认true
IS_LOGIN=true
# 登录账号
HOO_NAME=
# 登录密码
HOO_PASSWORD=
# 登录重试次数 默认5次
HOO_LOGIN_RETRY=
# 服务前缀
SERVICE_NAME=
# 资源前缀 用于日志查看器资源加载 需要带/开头
ASSET_URL=
# 数据连接 不填写则默认mysql
HOO_DATABASE_DEFAULT=
#-------------------------------------------------------
```

### arcanedev/log-viewer日志监控模块
- 执行初始化命令：php artisan log-viewer:publish
- 会生成配置文件：config/log-viewer.php
- 可更改配置文件中 route.attributes.prefix 的值，从而调整路由
- 注意：日志文件必须是laravel-2024-10-08.log这种格式，否则无法识别
- env配置
```bash
#--------------------hoo_hm api与依赖服务接口请求日志--------------------------
# 记录api请求日志时是否记录日志到数据库 默认记录到数据库（前提条件是数据库中存在日志表）
HM_API_LOG=true
# 记录api请求日志时 记录的用户唯一标识是取请求中的哪个字段 默认是member_id
HM_API_LOG_USER_FILED='member_id'
# 允许不记录日志的路由
HM_API_LOG_NOT_ROUTE=


# 记录程序访问第三方http api 时是否记录日志到数据库 默认记录到数据库（前提条件是数据库中存在日志表）
HM_HTTP_LOG=true
# hhttp日志开关 跑命令时的hhttp日志是否记录 默认false
HM_COMMAND_HTTP_LOG=false


# 记录程序访问数据库 时是否记录日志到数据库 默认记录到数据库（前提条件是数据库中存在日志表）
HM_SQL_LOG=true
# sql日志开关 跑命令时的sql是否记录  默认false
HM_SQL_COMMAND_LOG=false

# 入参出参 字符长度限制 如果超出就不再记录 防止数据库短期容量暴涨 默认5000
HM_API_HTTP_LOG_LENGTH=10000

# 日志清理设置
# api日志清理多久之前的日志 默认 60天前的
HM_API_LOG_CLEAN=60,
# hhttp日志清理多久之前的日志 默认 60天前的
HM_HPPT_LOG_CLEAN=60,
# sql日志清理多久之前的日志 默认 60天前的
HM_SQL_LOG_CLEAN=60,
#-------------------------------------------------------
```

## 快捷配置 必要配置 其它可依据情况添加
```bash
#--------------------CLOCKWORK--------------------------
# 限制环境 local 开发环境可进 test 测试环境可进 production 生产环境 且请求头中有灰度标识可进 其它环境不可进
# 是否开启CLOCKWORK服务
CLOCKWORK_ENABLE=true
# 收集的数据存储时效 单位：分钟
CLOCKWORK_STORAGE_EXPIRATION=120
# 【控制面板访问】是否开启第二层密码
CLOCKWORK_AUTHENTICATION=false
# 【控制面板访问】第二层密码（默认密码：VerySecretPassword）
CLOCKWORK_AUTHENTICATION_PASSWORD=
#-------------------------------------------------------

#--------------------hoo_hm--------------------------
HOO_ENABLE=true
# 登录账号
HOO_NAME=
# 登录密码
HOO_PASSWORD=
# 服务前缀
SERVICE_NAME=
# 资源前缀 用于日志查看器资源加载 需要带/开头
ASSET_URL=
# 数据连接 不填写则默认mysql
HOO_DATABASE_DEFAULT=
#-------------------------------------------------------
```

- 注意
- 如果开启鉴权插件-则需处理网关阻挡问题
