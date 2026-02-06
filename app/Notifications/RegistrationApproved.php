<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RegistrationApproved extends Notification
{
    use Queueable;

    private $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to the ' . config('app.name') . '!')
            ->greeting('Hello ' . $this->user->name . '!')
            ->line('Your registration has been officially approved by the university administration.')
            ->line('You now have full access to the alumni portal, including the community chat, event registration, and employment tracker.')
            ->action('Access Your Account', url('/login'))
            ->line('If you have any questions, feel free to reply to this email.')
            ->line('Welcome back to the community!');
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Account Approved',
            'message' => 'Your registration has been approved. Welcome to the portal!',
            'type' => 'success',
        ];
    }
}
