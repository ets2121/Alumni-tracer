<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMailCommand extends Command
{
    protected $signature = 'alumni:test-mail {email}';
    protected $description = 'Send a direct synchronous test email to verify SMTP settings';

    public function handle()
    {
        $email = $this->argument('email');
        $this->info("Attempting to send a direct SMTP test email to: {$email}...");

        try {
            Mail::raw('This is a direct SMTP test from the Alumni System to verify your mail configuration.', function ($message) use ($email) {
                $message->to($email)->subject('SMTP Diagnostic Test');
            });
            $this->info("Success! The email was accepted by the SMTP server.");
        } catch (\Exception $e) {
            $this->error("SMTP Failure: " . $e->getMessage());
            $this->comment("\nCheck your .env settings (MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD).");
            $this->comment("If using Gmail, ensure you are using an 'App Password' and not your regular password.");
        }
    }
}
