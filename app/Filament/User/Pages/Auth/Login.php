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
class Login extends BaseLogin
{
    // If you want to use a completely custom view
    // protected static string $view = 'filament.user.pages.auth.login';
    
    public function getTitle(): string | Htmlable
    {
        return __('Bursiyer Basvuru');
    }
    
    public function getHeading(): string | Htmlable
    {
        return __('Bursiyer Basvuru');
    }
    
    public function getSubheading(): string | Htmlable | null
    {
        return __('Hesabınıza giriş yapın');
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
            // ->icon('heroicon-o-at-symbol')
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
}