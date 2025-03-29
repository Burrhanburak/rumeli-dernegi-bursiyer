<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Auth\VerifyEmail;
use Illuminate\Support\Facades\Lang;
use Filament\Facades\Filament;

class VerifyTestNotification extends VerifyEmail
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public string $url;

     public function __construct(private readonly string $token)
     {}
  

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }
    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('E-posta Doğrulama')
            ->greeting('Merhaba ' . $notifiable->name . ',')
            ->line('Lütfen aşağıdaki butona tıklayarak e-posta adresinizi doğrulayın.')
            ->action('E-posta Adresini Doğrula', $this->verificationUrl($notifiable))
            ->line('Eğer bir hesap oluşturmadıysanız, başka bir işlem yapmanıza gerek yoktur.')
            ->line('Teşekkürler.')
            ->salutation('Saygılarımızla,');
    }

    protected function verificationUrl($notifiable): string
    {
        // Fix: Pass an array of parameters instead of a string
        return Filament::getVerifyEmailUrl($notifiable, []);
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
