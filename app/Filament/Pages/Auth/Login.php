<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as AdminLogin;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Checkbox;
use Filament\Facades\Filament;
use Filament\Actions\Action;
use Filament\Forms\Form;
use Filament\Pages\Dashboard;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;
use Illuminate\Contracts\Support\Htmlable;

class Login extends AdminLogin
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    // Optional: Disable navigation if this is a login page

    
    public function toResponse($request): RedirectResponse|Redirector
    {
        if (auth()->user()->is_admin) {
            return redirect()->to(Dashboard::getUrl(panel: 'admin'));
        }
 
        return parent::toResponse($request);
    }


    public function getTitle(): string | Htmlable
    {
        return __('Admin Giriş');
    }

    public function getHeading(): string | Htmlable
    {
        return __('Admin Giriş');
    }

    public function getSubheading(): string | Htmlable | null
    {
        return __('Hesabınıza giriş yapın');
    }
 
    public function getSubNavigation(): array
    {
        return [];
    }

    public function getCachedSubNavigation(): array
    {
        return $this->getSubNavigation();
    }

    public function getSubNavigationPosition(): ?SubNavigationPosition
    {
        return null;
    }
    



    // Optional: Disable navigation if this is a login page
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
 // New method to resolve widget data error
    public function getWidgetData(): array
    {
        return [];
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
            ->placeholder('E-posta adresinizi giriniz')
            ->validationMessages([
                'email.required' => 'Email alanı zorunludur.',
            ])
            ->email()
            ->required()
            ->autocomplete('username')
            ->autofocus();
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label(__('Şifre'))
            ->placeholder('Şifrenizi giriniz')
            ->validationMessages([
                'password.required' => 'Şifre alanı zorunludur.',
            ])
            ->password()
            ->required()
            ->autocomplete('current-password');
    }

    protected function getRememberFormComponent(): Component
    {
        return Checkbox::make('remember')
            ->label(__('Beni Hatırla'));
    }

    protected function getFormActions(): array
    {
        return [
            $this->getAuthenticateFormAction(),
            Action::make('userLogin')
                ->label('Kullanıcı Girişi')
                // ->color('secondary')
                ->url(route('filament.user.auth.login'))
        ];
    }

    

    protected function getAuthenticateFormAction(): Action
    {
        return Action::make('authenticate')
            ->label(__('Giriş Yap'))
            ->icon('heroicon-o-arrow-right-on-rectangle')
            ->submit('authenticate');
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'email' => $data['email'],
            'password' => $data['password'],
        ];
    }
}