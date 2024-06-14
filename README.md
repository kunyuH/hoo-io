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
