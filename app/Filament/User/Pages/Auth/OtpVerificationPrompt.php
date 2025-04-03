<?php

namespace App\Filament\User\Pages\Auth;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Form;
use Filament\Notifications\Auth\VerifyEmail;
use Filament\Pages\Auth\EmailVerification\EmailVerificationPrompt;
use App\Notifications\OtpNotification;

use Filament\Pages\SimplePage;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class OtpVerificationPrompt extends EmailVerificationPrompt
{
    use WithRateLimiting;

    protected static string $view = 'filament.user.pages.auth.otp-verification';

    public $otp = '';

    public function mount(): void
    {
        if ($this->getVerifiable()->hasVerifiedEmail()) {
            redirect()->intended(Filament::getUrl());
        }
    }

    protected function getVerifiable(): MustVerifyEmail
    {
        return auth()->user();
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

        // Generate OTP
        $otp = rand(100000, 999999);
        $user->otp_code = $otp;
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        // Send OTP notification
        $user->notify(new OtpNotification($otp));
    }

    public function getHeading(): string | Htmlable
    {
        return __('E-posta Adresinizi Doğrulayın');
    }

    public function getSubheading(): string | Htmlable
    {
        return __('Hesabınızı doğrulamak için lütfen e-posta adresinize gönderilen OTP kodunu girin.');
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
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();
            return null;
        }

        $user = auth()->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(Filament::getUrl());
        }

        $this->sendEmailVerificationNotification($user);

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

    public function verify()
    {
        $user = auth()->user();
        if ($user->verifyOtp($this->otp)) {
            return redirect()->intended(Filament::getUrl());
        } else {
            $this->addError('otp', 'Geçersiz veya süresi dolmuş OTP.');
        }
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

