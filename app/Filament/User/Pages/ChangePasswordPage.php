<?php

namespace App\Filament\User\Pages;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Filament\Notifications\Notification;
use Filament\Actions\Action;

class ChangePasswordPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-key'; // İkonu isteğe bağlı olarak ayarlayabilirsiniz
    protected static string $view = 'filament.user.pages.change-password-page'; // View'ı tekrar belirtiyoruz
    protected static ?string $title = 'Şifre Değiştir';
    protected static ?string $slug = 'change-password';

    // Bu sayfanın navigasyonda görünmemesi için:
    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
        // Middleware tarafından eklenen uyarı mesajını Filament notification olarak göster
        if (session()->has('warning')) {
            Notification::make()
                ->title('Bilgilendirme')
                ->body(session('warning'))
                ->warning()
                ->send();
            // Session'daki uyarıyı temizle ki tekrar görünmesin
            session()->forget('warning');
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('password')
                    ->label('Yeni Şifre')
                    ->password()
                    ->required()
                    ->rule(PasswordRule::min(8)
                        ->mixedCase('Yeni şifre en az bir büyük ve bir küçük harf içermelidir.')
                        ->numbers('Yeni şifre en az bir rakam içermelidir.')
                        ->symbols('Yeni şifre en az bir özel karakter içermelidir.'))
                    ->confirmed()
                    ->validationAttribute('Yeni Şifre'),
                TextInput::make('password_confirmation')
                    ->label('Yeni Şifre (Tekrar)')
                    ->password()
                    ->required()
                    ->validationAttribute('Yeni Şifre Tekrar'),
            ])
            ->statePath('data');
    }

    protected function getActions(): array
    {
        return [
            Action::make('updatePassword')
                ->label('Şifreyi Güncelle')
                ->action('save') // 'save' metodunu çağıracak
        ];
    }

    public function save(): void
    {
        $this->validate(); // Formdaki kurallara göre validate edecek

        $user = Auth::user();

        try {
            $user->password = Hash::make($this->data['password']);
            $user->must_change_password = false;
            $user->save();

            Notification::make()
                ->title('Şifreniz başarıyla güncellendi!')
                ->success()
                ->send();
            
            redirect()->route('filament.user.pages.user-dashboard');

        } catch (\Exception $e) {
            Notification::make()
                ->title('Şifre güncellenirken bir hata oluştu.')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    // Form kurallarını merkezi bir yerden almak için (getFormSchema içindeki rule() ile aynı ama daha derli toplu olabilir)
    protected function getFormRules(): array
    {
        return [
            'password' => ['required', 'confirmed', PasswordRule::min(8)->mixedCase()->numbers()->symbols()],
            'password_confirmation' => ['required'], // password_confirmation için ayrıca bir kurala gerek yok, confirmed hallediyor.
        ];
    }

    // Blade view dosyasını belirtmek için (standart Filament görünümü yerine)
    // Eğer özel bir Blade view kullanmak isterseniz bu metodu aktif edin.
    // protected function getView(): string
    // {
    //     return 'filament.pages.custom-change-password'; 
    // }
} 