<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RegistrationRejected extends Notification
{
    use Queueable;

    private $user;
    private $remarks;

    public function __construct($user, $remarks = null)
    {
        $this->user = $user;
        $this->remarks = $remarks;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->error()
            ->subject('Update on your Registration - ' . config('app.name'))
            ->greeting('Hello ' . $this->user->name . '!')
            ->line('We regret to inform you that your registration application for the ' . config('app.name') . ' has not been approved at this time.');

        if ($this->remarks) {
            $mail->line('**Administrative Remarks:**')
                ->line($this->remarks);
        }

        $mail->line('If you believe this is a mistake or would like to provide additional information for reconsideration, please contact the university administration.')
            ->line('Thank you for your interest in our community.');

        return $mail;
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Registration Rejected',
            'message' => 'Your registration application was not approved.',
            'remarks' => $this->remarks,
            'type' => 'error',
        ];
    }
}
