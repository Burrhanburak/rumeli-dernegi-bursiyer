<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Lang;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private readonly string $token)
    {}
 

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
            ->subject('Şifre Sıfırlama Bildirimi')
            ->greeting('Merhaba ' . $notifiable->name . ',')
            ->line('Hesabınız için bir şifre sıfırlama talebi aldığımız için bu e-postayı alıyorsunuz.')
            ->action('Şifreyi Sıfırla', $this->resetUrl($notifiable))
            ->line('Bu şifre sıfırlama bağlantısı ' . config('auth.passwords.'.config('auth.defaults.passwords').'.expire') . ' dakika içinde sona erecektir.')
            ->line('Eğer şifre sıfırlama talebinde bulunmadıysanız, başka bir işlem yapmanıza gerek yoktur.')
            ->salutation('Saygılarımızla,');
    }
    protected function resetUrl(mixed $notifiable): string
    {
        return Filament::getResetPasswordUrl($this->token, $notifiable);
    }
   
}
