<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerificationCodeNotification extends Notification
{
    use Queueable;

    private $code;
    private $type;

    public function __construct($code, $type)
    {
        $this->code = $code;
        $this->type = $type; // signup, email_update, password_update
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $action = match ($this->type) {
            'signup' => 'verifying your new account',
            'email_update' => 'updating your email address',
            'password_update' => 'changing your password',
            default => 'verifying your identity',
        };

        return (new MailMessage)
            ->subject('Your Verification Code - ' . config('app.name'))
            ->greeting('Hello!')
            ->line('You are ' . $action . '.')
            ->line('Please use the following 6-digit verification code to proceed:')
            ->line('**' . $this->code . '**')
            ->line('This code will expire in 5 minutes and can only be used once.')
            ->line('**Note:** If you did not request this, please ignore this email. Check your spam or junk folder if you don\'t see this email in your inbox.')
            ->salutation('Warm regards, ' . config('app.name') . ' Team');
    }
}
