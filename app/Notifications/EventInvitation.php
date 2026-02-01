<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventInvitation extends Notification implements ShouldQueue
{
    use Queueable;

    private $event;

    public function __construct($event)
    {
        $this->event = $event;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Exclusive Invitation: ' . $this->event->title)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('We are excited to invite you to our upcoming event: **' . $this->event->title . '**.')
            ->line('**Date:** ' . \Carbon\Carbon::parse($this->event->date)->format('M d, Y'))
            ->line('**Location:** ' . $this->event->location)
            ->line($this->event->description)
            ->action('View Event Details', url('/events/' . $this->event->id))
            ->line('Don\'t miss this opportunity to reconnect and network with your fellow alumni.')
            ->line('We hope to see you there!');
    }

    public function toArray($notifiable): array
    {
        return [
            'event_id' => $this->event->id,
            'title' => 'New Event Invitation',
            'message' => 'You are invited to ' . $this->event->title,
        ];
    }
}
