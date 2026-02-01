<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TracerStudyReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Action Required: University Tracer Study')
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('Your journey and success after graduation matter deeply to us.')
            ->line('We are currently conducting our annual Tracer Study to understand the career progression of our alumni and improve our academic programs.')
            ->line('Your participation is vital and will only take a few minutes of your time.')
            ->action('Complete Tracer Study', url('/tracer-study'))
            ->line('Thank you for your continued contribution to the university\'s growth.')
            ->salutation('Best regards, Alumni Relations Office');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'reminder',
            'title' => 'Tracer Study Pending',
            'message' => 'Please complete the annual tracer study to help us improve.',
        ];
    }
}
