<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InterviewScheduled extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Interview $interview)
    {
        $this->interview = $interview;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('Mülakat tarihi: ' . $this->interview->interview_date)
            ->action('Mülakata git', url('/'))
            ->line('Teşekkürler!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'interview_id' => $this->interview->id,
            'interview_date' => $this->interview->interview_date,
            'location' => $this->interview->location,
            'interviewer_name' => $this->interview->interviewer_name,
            'notes' => $this->interview->notes,
            'is_online' => $this->interview->is_online,
            'meeting_link' => $this->interview->meeting_link,
        ];
    }
}
