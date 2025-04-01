<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\Events\Registered;
use Filament\Facades\Filament;
use App\Notifications\VerifyTestNotification;

class SendEmailVerificationNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     * 
     * DISABLED: We're now handling this directly in the User model
     * to prevent duplicate emails.
     */
    public function handle(Registered $event): void
    {
        // This method is disabled because we're now handling email verification 
        // directly in the User model's sendEmailVerificationNotification method
        // Keeping this file for reference
        return;
        
        if ($event->user instanceof MustVerifyEmail && ! $event->user->hasVerifiedEmail()) {
            // Set locale to Turkish for consistent language in emails
            $locale = 'tr';
            app()->setLocale($locale);

            // Create and send notification
            $notification = new VerifyTestNotification($locale);
            $notification->url = Filament::getVerifyEmailUrl($event->user);

            $event->user->notify($notification);
        }
    }
}
