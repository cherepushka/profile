<?php

namespace App\Console\Commands;

use App\Jobs\ChangeDocumentsCrypt;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        ChangeDocumentsCrypt::dispatch(1, '1f7bbecfed4f85fc6260259192c220f055719dd71da4117b287f5306b3df8029')->delay(now()->addDay());

        return Command::SUCCESS;
    }
}
