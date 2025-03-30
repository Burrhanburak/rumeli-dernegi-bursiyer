<?php

namespace App\Filament\User\Pages\Auth;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\EmailVerification\EmailVerificationPrompt as BaseVerifyEmail;
use Illuminate\Contracts\Support\Htmlable;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Form;
use Filament\Notifications\Auth\VerifyEmail;

use Filament\Pages\SimplePage;
use Illuminate\Contracts\Auth\MustVerifyEmail;



class EmailVerificationPrompt extends BaseVerifyEmail
{
    protected static string $view = 'filament.user.pages.auth.verify-email';


    public function mount(): void
    {
        if ($this->getVerifiable()->hasVerifiedEmail()) {
            redirect()->intended(Filament::getUrl());
        }
    }
    protected function getVerifiable(): MustVerifyEmail
    {
        /** @var MustVerifyEmail */
        $user = Filament::auth()->user();

        return $user;
    }

    protected function sendEmailVerificationNotification(MustVerifyEmail $user): void
    {
        if ($user->hasVerifiedEmail()) {
            return;
        }

        if (! method_exists($user, 'notify')) {
            $userClass = $user::class;

            throw new Exception("Model [{$userClass}] does not have a [notify()] method.");
        }

        $notification = app(\App\Notifications\VerifyTestNotification::class, ['token' => '']);
        $notification->url = Filament::getVerifyEmailUrl($user);

        $user->notify($notification);
    }

   

    public function getHeading(): string | Htmlable
    {
        return __('E-posta Adresinizi Doğrulayın');
    }

    public function getSubheading(): string | Htmlable
    {
        return __('Hesabınızı doğrulamak için lütfen e-posta adresinize gönderilen doğrulama bağlantısını tıklayın.');
    }

    public function getCachedSubNavigation(): array
    {
        return [];
    }

    public function getSubNavigationPosition(): string
    {
        return 'before';
    }

    public function getWidgetData(): array
    {
        return [];
    }

    public function getHeader(): string | Htmlable | null
    {
        return null;
    }

    public function getFooter(): string | Htmlable | null
    {
        return null;
    }

    public function getCachedHeaderActions(): array
    {
        return [];
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function getVisibleHeaderWidgets(): array
    {
        return [];
    }

    public function getVisibleFooterWidgets(): array
    {
        return [];
    }

    public function resendNotification()
    {
        $user = auth()->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(Filament::getUrl());
        }

        $user->sendEmailVerificationNotification();

        Notification::make()
            ->title(__('Doğrulama e-postası gönderildi!'))
            ->success()
            ->send();
    }

    public function resendNotificationAction(): Action
    {
        return Action::make('resendNotification')
            ->label(__('Doğrulama E-postasını Tekrar Gönder'))
            ->button()
            ->action('resendNotification');
    }

    public function loginAction(): Action
    {
        return Action::make('login')
            ->label(__('Giriş Sayfasına Dön'))
            ->icon('heroicon-o-arrow-left')
            ->link()
            ->url(route('filament.user.auth.login'));
    }

    protected function getRateLimitedNotification(TooManyRequestsException $exception): ?Notification
    {
        return Notification::make()
            ->title(__('filament-panels::pages/auth/email-verification/email-verification-prompt.notifications.notification_resend_throttled.title', [
                'seconds' => $exception->secondsUntilAvailable,
                'minutes' => $exception->minutesUntilAvailable,
            ]))
            ->body(array_key_exists('body', __('filament-panels::pages/auth/email-verification/email-verification-prompt.notifications.notification_resend_throttled') ?: []) ? __('filament-panels::pages/auth/email-verification/email-verification-prompt.notifications.notification_resend_throttled.body', [
                'seconds' => $exception->secondsUntilAvailable,
                'minutes' => $exception->minutesUntilAvailable,
            ]) : null)
            ->danger();
    }
}
