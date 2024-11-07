<?php
namespace hoo\io\common\Console\Command;

use Illuminate\Support\Facades\Cache;

class RunCodeCommand extends BaseCommand
{
    protected $signature = 'hm:runCode {code-key}';

	// Command description
	protected $description = 'run code';

	// Execute the console command
	public function handle()
	{
        $code = Cache::get($this->argument('code-key'));

        # 去除php标识
        $code = preg_replace('/<\?php/', '', $code);
        $code = preg_replace('/\?>/', '', $code);

        # 执行
        eval($code);
	}

    function findSequence($goal) {
        // Command description
        function find($start, $history) {
            if (start == goal)
                return history;
            else if (start > goal)
                return null;
            else
                return find(start + 5, "(" + history + " + 5)") ||
                    find(start * 3, "(" + history + " * 3)");
        }
        return find(1, "1");
    }
}
