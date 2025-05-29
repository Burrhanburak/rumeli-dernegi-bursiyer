<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue; // Test için geçici olarak kaldırıldı
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewUserWelcomeEmail extends Mailable // implements ShouldQueue // Test için geçici olarak kaldırıldı
{
    use Queueable, SerializesModels;

    public User $user;
    public string $temporaryPassword;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $temporaryPassword)
    {
        $this->user = $user;
        $this->temporaryPassword = $temporaryPassword;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Burs Başvuru Sistemine Hoş Geldiniz!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.new_user_welcome',
            with: [
                'userName' => $this->user->name,
                'userEmail' => $this->user->email,
                'temporaryPassword' => $this->temporaryPassword,
                'loginUrl' => route('filament.user.auth.login'), // Veya kullanıcı panelinizin giriş URL'si
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
