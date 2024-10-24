<?php

namespace hoo\io\common\Listeners;

use hoo\io\common\Models\SqlLogModel;
use Illuminate\Database\Events\QueryExecuted;

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
     * @var
     */
    protected static $logChannel = null;

    /**
     * @param QueryExecuted $query
     * @param string $mode 处理模式(默认情况下使用解析模式，当无效时回退到常规模式)
     * @return void
     */
    public function handle(QueryExecuted $query, string $mode = self::MODEL__RESOLVE): void
    {
        try {
            handleStart:

            // 解析出执行的SQL语句

            switch ($mode){
                case self::MODEL__RESOLVE:
                    $executeSql = $this->resolveHandle($query);
                    break;
                default:
                    $executeSql = $this->normalHandle($query);
            }

            # 先暂存  请求完成后统一记录
            (new SqlLogModel())->log($query->time ?? 0,
                $query->connection->getDatabaseName(),
                $query->connectionName,
                $executeSql
                );

        } catch (\Throwable $e) {}
    }

    /**
     * 常规处理
     *
     * @param QueryExecuted $event
     * @return string
     */
    protected function normalHandle(QueryExecuted $event): string
    {
        $record = str_replace('?', '"%s"', $event->sql);
        $record = vsprintf($record, $event->bindings);
        $record = str_replace('\\', '', $record);

        return $record;
    }

    /**
     * 解析处理
     *
     * @param QueryExecuted $event
     * @return string
     */
    protected function resolveHandle(QueryExecuted $event): string
    {
        $sql = $event->sql;

        foreach ($this->formatBindings($event) as $key => $binding) {
            $regex = is_numeric($key)
                ? "/\?(?=(?:[^'\\\']*'[^'\\\']*')*[^'\\\']*$)/"
                : "/:{$key}(?=(?:[^'\\\']*'[^'\\\']*')*[^'\\\']*$)/";

            if ($binding === null) {
                $binding = 'null';
            } elseif (is_int($binding) || is_float($binding)) {
                $binding = (string) $binding;
            } else {
                $binding = $this->quoteStringBinding($event, $binding);
            }

            $sql = preg_replace($regex, $binding, $sql, is_numeric($key) ? 1 : -1);
        }

        return $sql;
    }

    /**
     * Format the given bindings to strings.
     *
     * @param QueryExecuted $event
     * @return array
     */
    protected function formatBindings(QueryExecuted $event): array
    {
        return $event->connection->prepareBindings($event->bindings);
    }

    /**
     * Add quotes to string bindings.
     *
     * @param QueryExecuted $event
     * @param string $binding
     * @return string
     */
    protected function quoteStringBinding(QueryExecuted $event, string $binding): string
    {
        try {
            return $event->connection->getPdo()->quote($binding);
        } catch (\PDOException $e) {
            return '';
        }

        // Fallback when PDO::quote function is missing...
        $binding = \strtr($binding, [
            chr(26) => '\\Z',
            chr(8)  => '\\b',
            '"'     => '\"',
            "'"     => "\'",
            '\\'    => '\\\\',
        ]);

        return "'" . $binding . "'";
    }
}
