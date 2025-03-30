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
     */
    public function handle(Registered $event): void
    {
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
