<?php

namespace App\Filament\User\Pages\Auth;

use Filament\Pages\Auth\Register as BaseRegister;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Forms\Components\Checkbox;
use Illuminate\Contracts\Support\Htmlable;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Actions\Action;

use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;
use Ysfkaya\FilamentPhoneInput\Infolists\PhoneEntry;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

class Register extends BaseRegister 
{
    // protected static string $view = 'filament.user.pages.auth.register';
    
    protected function getCachedSubNavigation(): array
    {
        return [];
    }

    public function getTitle(): string | Htmlable
    {
        return __('Bursiyer Başvuru');
    }

    public function getHeading(): string | Htmlable
    {
        return __('Bursiyer Başvuru');
    }
    
    public function getSubheading(): string | Htmlable | null
    {
        return __('Hesabınız yok mu? Hemen kayıt olun.');
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getTCKimlikFormComponent(),
                $this->getDogumTarihiFormComponent(),
                $this->getPhoneFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                $this->getTermsCheckboxComponent(),
            ])
            ->statePath('data');
    }
 
    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('E-posta')
            ->placeholder('E-posta adresinizi giriniz')
            ->required()
            ->unique(User::class)
            ->validationMessages([
                'unique' => 'Bu e-posta adresi zaten kayıtlı.',
            ])
            ->email();
        
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('Şifre')
            ->placeholder('Şifrenizi giriniz')
            ->required()
            ->password()
            ->minLength(8)
            ->rules([
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'
            ])
            ->validationMessages([
                'min' => 'Şifre en az 8 karakter uzunluğunda olmalıdır.',
                'regex' => 'Şifre: büyük, küçük harf, rakam ve özel karakter içermelidir.'
            ]);
          
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return TextInput::make('password_confirmation')
            ->label('Şifre Tekrar')
            ->placeholder('Şifrenizi tekrar giriniz')
            ->required()
            ->password()
            ->same('password')
            ->validationMessages([
                'same' => 'Şifreler eşleşmiyor.'
            ]);
            
    }

    protected function getTCKimlikFormComponent(): Component
    {
        return TextInput::make('tc_kimlik_no')
            ->label('TC Kimlik No')
            ->placeholder('TC Kimlik numaranızı giriniz')
            ->required()
            ->unique(User::class)
            ->maxLength(11)
            ->minLength(11)
            ->numeric()
            ->rules(['regex:/^[1-9]{1}[0-9]{9}[02468]{1}$/'])
            ->validationMessages([
                'unique' => 'Bu TC Kimlik Numarası zaten kayıtlı.',
                'regex' => 'Geçerli bir TC Kimlik Numarası girmelisiniz.'
            ]);
          
    }

    protected function getDogumTarihiFormComponent(): Component
    {
        return DatePicker::make('dogum_tarihi')
            ->label('Doğum Tarihi')
            ->required()
            ->maxDate(now()->subYears(18))
            ->validationMessages([
                'max' => '18 yaşından küçükler kayıt olamaz.'
            ]);
    }

    protected function getPhoneFormComponent(): Component
    {
        return PhoneInput::make('phone')
            ->defaultCountry('tr')
            ->initialCountry('tr')
            ->locale('tr')
            ->countrySearch(false)
            ->label('Telefon Numarası')
            ->required()
            ->unique(User::class)
            ->validationMessages([
                'unique' => 'Bu telefon numarası zaten kayıtlı.'
            ]);
          
    }

    protected function getTermsCheckboxComponent(): Component
    {
        return Checkbox::make('terms')
            ->label('Kullanım Koşulları ve Gizlilik Politikasını okudum, kabul ediyorum.')
            ->required()
            ->validationMessages([
                'required' => 'Kullanım Koşulları\'nı kabul etmelisiniz.'
            ]);
          
    }

    public function getRenderHookScopes(): array
    {
        return [
            'panels:auth.register',
        ];
    }

    protected function getSubNavigationPosition(): ?int
    {
        return null;
    }
    
    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }
    
    public function getRegisterFormAction(): Action
    {
        return Action::make('register')
            ->label('Kayıt Ol')
            ->submit('register');
    }
    
    public function loginAction(): Action
    {
        return Action::make('login')
            ->label('Giriş Yap')
            ->url(route('filament.user.auth.login'));
    }
}