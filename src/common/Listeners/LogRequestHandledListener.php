<?php

namespace hoo\io\common\Listeners;

use hoo\io\common\Models\SqlLogModel;

final class LogRequestHandledListener
{

    public function handle(): void
    {
        try {
            (new SqlLogModel())->logSave();
        } catch (\Throwable $e) {}
    }

}
