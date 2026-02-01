<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Notifications\TracerStudyReminder;
use Illuminate\Support\Facades\Notification;

class SendTracerStudyReminders extends Command
{
    protected $signature = 'alumni:remind-tracer';
    protected $description = 'Send tracer study reminders to all active alumni';

    public function handle()
    {
        $this->info('Finding active alumni...');

        $alumni = User::where('role', 'alumni')
            ->where('status', 'active')
            ->get();

        if ($alumni->isEmpty()) {
            $this->warn('No active alumni found.');
            return;
        }

        $this->info('Sending notifications to ' . $alumni->count() . ' alumni...');

        Notification::send($alumni, new TracerStudyReminder());

        $this->info('Reminders sent successfully!');
    }
}
