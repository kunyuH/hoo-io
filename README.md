# laravel-io

### 简介
各类io扩展
- 数据库
- http

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

### http客户端调用
```php
$service = config('http_service.inner_service');
$api = '/api/test';
$res = (new HttpInnerService($service))
    ->setReq($api,'GET')
    ->send()
    ->get();
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
# 【控制面板访问】第一层密码
CLOCKWORK_AUTH_TOKEN=123456
# 【控制面板访问】是否开启第二层密码
CLOCKWORK_AUTHENTICATION=
# 【控制面板访问】第二层密码（默认密码：VerySecretPassword）
CLOCKWORK_AUTHENTICATION_PASSWORD=
#-------------------------------------------------------
```
- 配置收集的数据清理脚本
- \App\Console\Kernel::schedule方法中增加
```php
    // 本定时任务的作用是清理过期日志文件
    // 具体时间配置见env配置步骤中的CLOCKWORK_STORAGE_EXPIRATION项 默认7天
    $schedule->command(ClockworkCleanCommand::class)->hourly();
```
- 注意
- 如果开启鉴权插件-则需要在用户中心将url加入
- /clockwork/app
- /__clockwork/latest
- /__clockwork/auth