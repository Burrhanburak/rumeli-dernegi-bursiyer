<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OtpNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private readonly string $otp)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
        ->subject('E-posta Doğrulama')
        ->greeting('Merhaba ' . $notifiable->name . ',')
        ->line('Lütfen aşağıdaki OTP kodunu kullanarak e-posta adresinizi doğrulayın.')
        ->line('OTP Kodu: **' . $this->otp . '**')
        ->line('Bu kod 10 dakika boyunca geçerlidir.')
        ->line('Eğer bir hesap oluşturmadıysanız, başka bir işlem yapmanıza gerek yoktur.')
        ->line('Teşekkürler.')
        ->salutation('Saygılarımızla,');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
