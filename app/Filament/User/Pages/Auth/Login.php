<?php

namespace App\Filament\User\Pages\Auth;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Actions\Action;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\Support\Htmlable;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\LoginResponse;
use Filament\Contracts\Auth\FilamentUser;
use Filament\Notifications\Notification as FilamentNotification;


class Login extends BaseLogin
{
    // If you want to use a completely custom view

   
    public function request(): void
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('filament-panels::pages/auth/login.notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->body(array_key_exists('body', __('filament-panels::pages/auth/login.notifications.throttled') ?: []) ? __('filament-panels::pages/auth/login.notifications.throttled.body', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]) : null)
                ->danger()
                ->send();
        }   
    }
    public function getHeading(): string | Htmlable
    {
        return __('Giriş Yapın');
    }

  
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ])
            ->statePath('data');
    }
    
    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label(__('Email'))
            // ->suffixIcon('heroicon-o-at-symbol')
            ->placeholder('E-posta adresinizi giriniz')
            ->email()
            ->required()
            ->autocomplete('username')
            ->autofocus();
          
    }
    
    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label(__('Şifre'))
            // ->icon('heroicon-o-lock-closed')
            ->revealable()
            ->placeholder('Şifrenizi giriniz')
            ->password()
            ->required()
            ->autocomplete('current-password');
    }
    
    protected function getRememberFormComponent(): Component
    {
        return Checkbox::make('remember')
            ->label(__('Beni Hatırla'));
    }
    
    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }
    
    public function registerAction(): Action
    {
        return Action::make('register')
            ->label(__('Hesap oluştur'))
            ->icon('heroicon-o-user-plus')
            ->url(route('filament.user.auth.register'));
    }
    
    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            $this->getAuthenticateFormAction(),
            $this->getForgotPasswordFormAction(),
        ];
    }
    
    protected function getAuthenticateFormAction(): Action
    {
        return Action::make('authenticate')
            ->label(__('Giriş Yap'))
            ->icon('heroicon-o-arrow-right-on-rectangle')
            ->submit('authenticate');
    }
    
    protected function getForgotPasswordUrl(): ?string
    {
        // Use filament's method to get the correct URL with your custom prefix
        return filament()->getRequestPasswordResetUrl();
    }
    
    protected function getForgotPasswordFormAction(): Action
    {
        return Action::make('forgotPassword')
            ->label(__('Şifremi unuttum'))
            ->icon('heroicon-o-question-mark-circle')
            ->url($this->getForgotPasswordUrl());
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'email' => $data['email'],
            'password' => $data['password'],
        ];
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $data = $this->form->getState();

        if (! Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
            $this->throwFailureValidationException();
        }

        $user = Filament::auth()->user();

        if (
            ($user instanceof FilamentUser) &&
            (! $user->canAccessPanel(Filament::getCurrentPanel()))
        ) {
            Filament::auth()->logout();

            $this->throwFailureValidationException();
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }

    protected function getRateLimitedNotification(TooManyRequestsException $exception): ?FilamentNotification
    {
        return FilamentNotification::make()
            ->title(__('Çok fazla giriş denemesi'))
            ->body(__('Lütfen :seconds saniye sonra tekrar deneyin.', [
                'seconds' => $exception->secondsUntilAvailable,
            ]))
            ->danger();
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.email' => __('Bu bilgiler kayıtlarımızla eşleşmiyor.'),
        ]);
    }

   

}