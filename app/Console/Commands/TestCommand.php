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
        ChangeDocumentsCrypt::dispatchSync(1, '1efae76d0beb7959387780c8675cc8a42bd309bf2df738673a0a9902709cbb50');

        return Command::SUCCESS;
    }
}
