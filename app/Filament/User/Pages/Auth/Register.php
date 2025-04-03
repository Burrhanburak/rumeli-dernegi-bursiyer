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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;
use Ysfkaya\FilamentPhoneInput\Infolists\PhoneEntry;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\Events\Registered;
use Exception;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\VerifyEmail;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;

class Register extends BaseRegister 
{
    use WithRateLimiting;
    
    protected static string $model = User::class;

    protected string $userModel;

    public function mount(): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        $this->callHook('beforeFill');

        $this->form->fill();

        $this->callHook('afterFill');
    }

    protected function getModel(): string
    {
        return static::$model;
    }

    // protected static string $view = 'filament.user.pages.auth.register';
    
    protected function getCachedSubNavigation(): array
    {
        return [];
    }

    public function getTitle(): string | Htmlable
    {
        return __('Hesap Oluştur');
    }

    public function getHeading(): string | Htmlable
    {
        return __('Hesap Oluştur');
    }
  
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getNameFormComponent(),
                $this->getSurnameFormComponent(),
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
 
    protected function getNameFormComponent(): Component
    {
        return TextInput::make('name')
            ->label('Ad')
            ->placeholder('Adınızı giriniz')
            ->required()
            ->maxLength(255)
            ->validationMessages([
                'required' => 'Ad alanı zorunludur.',
                'max' => 'Ad en fazla 255 karakter olabilir.'
            ]);
    }

    protected function getSurnameFormComponent(): Component
    {
        return TextInput::make('surname')
            ->label('Soyad')
            ->placeholder('Soyadınızı giriniz')
            ->required()
            ->maxLength(255)
            ->validationMessages([
                'required' => 'Soyad alanı zorunludur.',
                'max' => 'Soyad en fazla 255 karakter olabilir.'
            ]);
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
            ->revealable()
            ->rule(Password::default())
            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
            
            ->minLength(8)
            // ->rules([
            //     'min:8',
            //     'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'
            // ])
            ->validationMessages([
                'min' => 'Şifre en az 8 karakter uzunluğunda olmalıdır.',
                'regex' => 'Şifre: büyük, küçük harf, rakam ve özel karakter içermelidir.'
            ])
            ->helperText('Güçlü bir şifre için:')
            ->helperText('• En az 8 karakter')
            ->helperText('• En az bir büyük harf')
            ->helperText('• En az bir küçük harf')
            ->helperText('• En az bir rakam')
            ->helperText('• En az bir özel karakter (@$!%*?&)');
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return TextInput::make('password_confirmation')
            ->label('Şifre Tekrar')
            ->placeholder('Şifrenizi tekrar giriniz')
             ->required()
            ->password()
            ->revealable()
              ->dehydrated(false)
            ->same('password')
            ->validationMessages([
                'same' => 'Şifreler eşleşmiyor.'
            ]);
            
    }

    protected function getTCKimlikFormComponent(): Component
    {
        return TextInput::make('national_id')
            ->label('TC Kimlik No')
            ->placeholder('TC Kimlik numaranızı giriniz')
            ->required()
            ->unique(User::class)
            ->maxLength(11)
            ->minLength(11)
            ->numeric()
            // ->rules(['regex:/^[1-9]{1}[0-9]{9}[02468]{1}$/'])
            ->validationMessages([
                'unique' => 'Bu TC Kimlik Numarası zaten kayıtlı.',
                'regex' => 'Geçerli bir TC Kimlik Numarası girmelisiniz.'
            ]);
          
    }

    protected function getDogumTarihiFormComponent(): Component
    {
        return DatePicker::make('birth_date')
            ->label('Doğum Tarihi')
            ->native(false)
            ->locale('tr')
            ->displayFormat('d/m/Y')
            ->required()
            ->placeholder('Doğum tarihinizi giriniz')
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

    public function register(): ?RegistrationResponse
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $user = $this->wrapInDatabaseTransaction(function () {
            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeRegister($data);

            $this->callHook('beforeRegister');

            $user = $this->handleRegistration($data);

            $this->form->model($user)->saveRelationships();

            $this->callHook('afterRegister');

            return $user;
        });

        event(new Registered($user));

        Filament::auth()->login($user);

        session()->regenerate();

        return app(RegistrationResponse::class);
    }

    protected function getRateLimitedNotification(TooManyRequestsException $exception): ?Notification
    {
        return Notification::make()
            ->title(__('filament-panels::pages/auth/register.notifications.throttled.title', [
                'seconds' => $exception->secondsUntilAvailable,
                'minutes' => $exception->minutesUntilAvailable,
            ]))
            ->body(array_key_exists('body', __('filament-panels::pages/auth/register.notifications.throttled') ?: []) ? __('filament-panels::pages/auth/register.notifications.throttled.body', [
                'seconds' => $exception->secondsUntilAvailable,
                'minutes' => $exception->minutesUntilAvailable,
            ]) : null)
            ->danger();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRegistration(array $data): Model
    {
        return $this->getUserModel()::create($data);
    }

    protected function sendEmailVerificationNotification(Model $user): void
    {
        if (! $user instanceof MustVerifyEmail) {
            return;
        }

        if ($user->hasVerifiedEmail()) {
            return;
        }

        // Let the User model handle the OTP generation and sending
        // This will use the OTP method instead of the link-based verification
        $user->sendEmailVerificationNotification();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeRegister(array $data): array
    {
        // Remove 'terms' field as it doesn't exist in the database
        if (isset($data['terms'])) {
            unset($data['terms']);
        }
        
        // Also remove password_confirmation if it exists
        if (isset($data['password_confirmation'])) {
            unset($data['password_confirmation']);
        }
        
        return $data;
    }

    protected function getUserModel(): string
    {
        return static::$model;
    }
}