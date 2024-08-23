<?php
namespace hoo\io\common\Command;

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

        # 执行
        eval($code);
	}
}
