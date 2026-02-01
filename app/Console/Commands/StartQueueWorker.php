<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class StartQueueWorker extends Command
{
    protected $signature = 'alumni:worker';
    protected $description = 'Start the background queue worker for emails';

    public function handle()
    {
        $this->info('Starting background queue worker...');
        $this->call('queue:work', [
            '--stop-when-empty' => true,
        ]);
        $this->info('Queue processing complete.');
    }
}
