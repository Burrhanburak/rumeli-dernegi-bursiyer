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
    public function handle(object $event): void
    {
        if ($event->user instanceof MustVerifyEmail && ! $event->user->hasVerifiedEmail()) {
            // @TODO Get the user's preferred locale
            $locale = app()->getLocale();

            $notification = new VerifyTestNotification($locale);
            $notification->url = Filament::getVerifyEmailUrl($event->user);

            $event->user->notify($notification);
        }
    }
}
