<?php

namespace App\Filament\User\Pages\Auth;

use Exception;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Auth\CanResetPassword;
use App\Notifications\ResetPasswordNotification; 
use Filament\Forms\Components\Component;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Pages\Auth\PasswordReset\RequestPasswordReset as BaseRequestPasswordReset;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Action;
use Closure;

class RequestPasswordReset extends BaseRequestPasswordReset
{
    protected static bool $shouldRegisterNavigation = false;

    public function getVisibleHeaderWidgets(): array
    {
        return [];
    }

    public function getVisibleFooterWidgets(): array
    {
        return [];
    }

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Giriş Yap';
    protected static ?string $navigationLabel = 'Şifre Sıfırlama';

 
    public function LinkAction(): Action
    {
        return Action::make('link')
            ->label('Giriş Sayfasına Dön')
            ->url(route('filament.user.auth.login'));
    }



    protected function getSubNavigationPosition(): ?int
    {
        return null;
    }
    
    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }
    
    
 
    public function getHeading(): string | Htmlable
    {
        return __('Şifre Sıfırlama');
    }

    public function getSubheading(): string | Htmlable | null
    {
        return __('Şifre sıfırlama bağlantısını almak için e-posta adresinizi girin.');
    }
    
    public function getHeader(): string | Htmlable | null
    {
        return null;
    }
    
    public function getFooter(): string | Htmlable | null
    {
        return null;
    }
    
    public function getLink(): string | Htmlable | null
    {
        return __('Giriş Sayfasına Dön');
    }
    
    public function getCachedSubNavigation(): array
    {
        return [];
    }
    public function getCachedNavigationLabel(): string | Htmlable
    {
        return __('Şifre Sıfırlama');
    }
 
    public function getWidgetData(): array
    {
        return [
            'email' => $this->form->getState()['email'] ?? null ,
        ];
    }
    public function getFormModel(): string
    {
        return 'email';
    }
    public function getFormSchema(): array
    {
        return [
            $this->getEmailFormComponent(),
        ];
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            $this->getRequestFormAction(),
        ];
    }

    /**
     * Get the primary form action button
     */
    protected function getRequestFormAction(): Action
    {
        return Action::make('request')
            ->label(__('Şifre Sıfırlama Bağlantısı Gönder'))
            ->submit('request');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getEmailFormComponent(),
            ])
            ->statePath('data');
    }
 
    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label(__('E-posta adresi'))
            
            ->email()
            ->placeholder(__('E-posta adresinizi girin'))
            ->maxLength(255);
    }
  
    public function getCachedTitle(): string | Htmlable
    {
        return __('Şifre Sıfırlama');
    }
    

    public function request(): void
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('filament-panels::pages/auth/password-reset/request-password-reset.notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->body(array_key_exists('body', __('filament-panels::pages/auth/password-reset/request-password-reset.notifications.throttled') ?: []) ? __('filament-panels::pages/auth/password-reset/request-password-reset.notifications.throttled.body', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]) : null)
                ->danger()
                ->send();
 
            return;
        }
 
        $data = $this->form->getState();
        $status = Password::broker(Filament::getAuthPasswordBroker())->sendResetLink(
            $data,
            function (CanResetPassword $user, string $token): void {
                if (! method_exists($user, 'notify')) {
                    $userClass = $user::class;
                    throw new Exception("Model [{$userClass}] does not have a [notify()] method.");
                }
 
                $notification = new ResetPasswordNotification($token); 
                $notification->url = Filament::getResetPasswordUrl($token, $user); 
                $user->notify($notification);
            },
        );
 
        if ($status !== Password::RESET_LINK_SENT) {
            Notification::make()
                ->title(__($status))
                ->danger()
                ->send();
 
            return;
        }
 
        Notification::make()
            ->title(__($status))
            ->success()
            ->send();
 
        $this->form->fill();
    }

    public function loginAction(): Action
    {
        return Action::make('login')
            ->label(__('Giriş Sayfasına Dön'))
            ->icon('heroicon-o-arrow-left')
            ->link()
            ->url(route('filament.user.auth.login'));
    }
}






// <?php

// namespace App\Filament\User\Pages\Auth;

// use Exception;
// use Filament\Facades\Filament;
// use Filament\Notifications\Notification;
// use Illuminate\Support\Facades\Password;
// use Illuminate\Contracts\Auth\CanResetPassword;
// use App\Notifications\ResetPasswordNotification; 

// use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
// use Filament\Pages\Auth\PasswordReset\RequestPasswordReset as BaseRequestPasswordReset;
 
// class RequestPasswordReset extends BaseRequestPasswordReset
// {
//     protected static ?string $navigationIcon = 'heroicon-o-document-text';

//     protected static string $view = 'filament.user.pages.auth.request-password-reset';

//     public function request(): void
//     {
//         try {
//             $this->rateLimit(2);
//         } catch (TooManyRequestsException $exception) {
//             Notification::make()
//                 ->title(__('filament-panels::pages/auth/password-reset/request-password-reset.notifications.throttled.title', [
//                     'seconds' => $exception->secondsUntilAvailable,
//                     'minutes' => ceil($exception->secondsUntilAvailable / 60),
//                 ]))
//                 ->body(array_key_exists('body', __('filament-panels::pages/auth/password-reset/request-password-reset.notifications.throttled') ?: []) ? __('filament-panels::pages/auth/password-reset/request-password-reset.notifications.throttled.body', [
//                     'seconds' => $exception->secondsUntilAvailable,
//                     'minutes' => ceil($exception->secondsUntilAvailable / 60),
//                 ]) : null)
//                 ->danger()
//                 ->send();
 
//             return;
//         }
 
//         $data = $this->form->getState();
//         $status = Password::broker(Filament::getAuthPasswordBroker())->sendResetLink(
//             $data,
//             function (CanResetPassword $user, string $token): void {
//                 if (! method_exists($user, 'notify')) {
//                     $userClass = $user::class;
//                     throw new Exception("Model [{$userClass}] does not have a [notify()] method.");
//                 }
 
//                 $notification = new ResetPasswordNotification($token); 
//                 $notification->url = Filament::getResetPasswordUrl($token, $user); 
//                 $user->notify($notification);
//             },
//         );
 
//         if ($status !== Password::RESET_LINK_SENT) {
//             Notification::make()
//                 ->title(__($status))
//                 ->danger()
//                 ->send();
 
//             return;
//         }
 
//         Notification::make()
//             ->title(__($status))
//             ->success()
//             ->send();
 
//         $this->form->fill();
//     }
// }