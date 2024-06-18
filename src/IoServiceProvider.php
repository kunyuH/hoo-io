<?php

namespace hoo\io;
use hoo\io\database\services\BuilderMacroSql;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Query\Builder as QueryBuilder;

class IoServiceProvider extends ServiceProvider
{
    public function register()
    {
        //注册 sql查询服务
        QueryBuilder::macro('getSqlQuery', function () {
            return (new BuilderMacroSql())->getSqlQuery($this);
        });
    }
}