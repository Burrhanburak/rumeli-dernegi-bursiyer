<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;
use Ysfkaya\FilamentPhoneInput\Infolists\PhoneEntry;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Actions\ExportAction;
use App\Filament\Exports\UserExporter;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Notifications\Notification;


class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationGroup = 'Kullanıcı Yönetimi';
    
    protected static ?int $navigationSort = 5;
    
    protected static ?string $navigationLabel = 'Kullanıcılar';

    protected static ?string $navigationBadgeTooltip = 'Aktif Kullanıcılar';

    protected static ?string $title = 'Kullanıcılar';

    protected static ?string $breadcrumb = 'Kullanıcılar';

    protected static ?string $breadcrumbParent = 'Kullanıcı Yönetimi';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Section::make('Kullanıcı Bilgileri')
            ->icon('heroicon-o-user')
            ->collapsible()
                ->description('Kişisel bilgilerinizi bu alandan düzenleyebilirsiniz.')
                ->schema([
                    // İlk satır - Ad ve Soyad
                    Forms\Components\TextInput::make('name')
                        ->label('Ad')
                        ->placeholder('Adınızı girin')
                        ->required()
                        ->default(Auth::user()->name)
                        ->maxLength(255),
                    
                    Forms\Components\TextInput::make('surname')
                        ->label('Soyad')
                        ->placeholder('Soyadınızı girin')
                        ->required()
                        ->default(Auth::user()->surname)
                        ->maxLength(255),
                    
                    // İkinci satır - Kimlik ve Doğum Tarihi
                    Forms\Components\TextInput::make('national_id')
                        ->label('T.C. Kimlik No')
                        ->placeholder('T.C. Kimlik Numaranız')
                        ->required()
                        ->numeric()
                        ->disabled('edit')
                        ->default(Auth::user()->national_id)
                        ->length(11),
                    
                    Forms\Components\DatePicker::make('birth_date')
                        ->label('Doğum Tarihi')
                        ->native(false) 
                        ->displayFormat('d/m/Y')
                        ->placeholder('Doğum Tarihiniz')
                        ->default(Auth::user()->birth_date),
                    
                    // Üçüncü satır - E-posta ve Doğrulama
                    Forms\Components\TextInput::make('email')
                        ->label('E-posta')
                        ->placeholder('E-posta adresinizi girin')
                        ->email()
                        ->required()
                        ->default(Auth::user()->email)
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),
                    
                    Forms\Components\DateTimePicker::make('email_verified_at')
                        ->label('E-posta Doğrulama Tarihi')
                        ->disabled(),
                    
                    // Dördüncü satır - Telefon ve Profil Fotoğrafı
                    PhoneInput::make('phone')
                        ->defaultCountry('tr')
                        ->initialCountry('tr')
                        ->placeholder('Telefon numaranızı giriniz')
                        ->locale('tr')
                        ->countrySearch(false)
                        ->label('Telefon Numarası')
                        ->required()
                        ->rules(['phone:TR'])
                        ->validationMessages([
                            'phone' => 'Lütfen geçerli bir telefon numarası girin.'
                        ])
                        ->unique(User::class, ignoreRecord: true),
                    
                    Forms\Components\FileUpload::make('image')
                        ->label('Profil Fotoğrafı')
                        ->image()
                        ->directory('profile-photos')
                        ->visibility('public')
                        ->maxSize(1024) // 1MB
                        ->placeholder('Profil fotoğrafınızı değiştiriniz')
                        ->uploadingMessage('Yükleniyor...')
                        ->circleCropper()
                        ->openable()
                        ->reorderable(false)
                        ->avatar(),
                    
                    // Beşinci satır - Yönetici yetkisi
                    Forms\Components\Toggle::make('is_admin')
                        ->label('Yönetici Yetkisi')
                        ->default(false)
                        ->helperText('Kullanıcıya yönetici yetkisi verir')
                        ->onColor('success')
                        ->offColor('danger')
                        ->columnSpanFull(),
                        
                ])
                ->columns(2),
            
            // Adres bilgileri bölümü
            Forms\Components\Section::make('Adres Bilgileri')
                ->description('İletişim adresinizi bu alandan düzenleyebilirsiniz.')
                ->icon('heroicon-o-map-pin')
                ->collapsible()
                ->schema([
                    Forms\Components\Textarea::make('address')
                        ->label('Adres')
                        ->placeholder('Adresinizi giriniz')
                        ->maxLength(255)
                        ->columnSpanFull(),
                    
                    Forms\Components\TextInput::make('city')
                        ->label('Şehir')
                        ->placeholder('Şehirinizi giriniz')
                        ->maxLength(100),
                    
                    Forms\Components\TextInput::make('postal_code')
                        ->label('Posta Kodu')
                        ->placeholder('Posta kodunuzu giriniz')
                        ->maxLength(20),
                ])
                ->columns(2),
            
            // Şifre değiştirme bölümü
            Forms\Components\Section::make('Şifre Değiştir')
                ->description('Güçlü bir şifre kullanmanız önerilir.')
                ->icon('heroicon-o-lock-closed')
                ->collapsible()
                ->collapsed() // Başlangıçta kapalı olması için
                ->schema([
                    Forms\Components\TextInput::make('current_password')
                        ->label('Mevcut Şifre')
                        ->placeholder('Mevcut şifrenizi giriniz')
                        ->password()
                        ->revealable()
                        ->dehydrated(false)
                        ->rule('current_password')
                        ->columnSpanFull(),
                    
                    Forms\Components\TextInput::make('password')
                        ->label('Yeni Şifre')
                        ->placeholder('Yeni şifrenizi giriniz')
                        ->password()
                        ->revealable()
                        ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                        ->dehydrated(fn (?string $state): bool => filled($state))
                        ->rule('confirmed')
                        ->minLength(8)
                        ->maxLength(255)
                        ->helperText('En az 8 karakter içeren güçlü bir şifre oluşturun.'),
                    
                    Forms\Components\TextInput::make('password_confirmation')
                        ->label('Yeni Şifre Tekrar')
                        ->placeholder('Yeni şifrenizi tekrar giriniz')
                        ->password()
                        ->revealable()
                        ->dehydrated(false),
                ])
                ->columns(2),
        ]);
          
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('image')
                    ->label('Fotoğraf')
                    ->circular()
                    ->defaultImageUrl(function ($record) {
                        $name = trim(($record->name ?? '') . ' ' . ($record->surname ?? ''));
                        if (empty($name)) $name = 'Kullanıcı';
                        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&size=128&background=808080&color=ffffff&bold=true&length=2';
                    }),
                Tables\Columns\TextColumn::make('name')
                    ->label('Ad')
                    ->searchable(),
                Tables\Columns\TextColumn::make('surname')
                    ->label('Soyad')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable(),
                Tables\Columns\TextColumn::make('is_admin')
                    ->label('Yetki')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Yönetici' : 'Kullanıcı')
                    ->color(fn (bool $state): string => $state ? 'success' : 'info'),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->label('E-posta Doğrulandı')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma Tarihi')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Güncellenme Tarihi')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('is_admin')
                    ->label('Kullanıcı Tipi')
                    ->form([
                        Forms\Components\Select::make('is_admin')
                            ->label('Kullanıcı Tipi')
                            ->options([
                                '1' => 'Yöneticiler',
                                '0' => 'Normal Kullanıcılar',
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['is_admin'] !== null,
                                fn (Builder $query): Builder => $query->where('is_admin', $data['is_admin']),
                            );
                    }),
                Tables\Filters\Filter::make('verified')
                    ->label('Doğrulanmış')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at'))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Görüntüle'),
                Tables\Actions\EditAction::make()
                    ->label('Düzenle'),
                Tables\Actions\DeleteAction::make()
                    ->label('Sil')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Kullanıcı Sil')
                    ->modalDescription('Bu kullanıcıyı silmek istediğinize emin misiniz?')
                    ->modalCancelActionLabel('İptal')
                    ->modalSubmitActionLabel('Sil')
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Silme Sebebi')
                            ->required(),
                    ])
                    ->action(function (User $record) {
                        $record->delete();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label(' Tümünü Sil')
                ])
                ->label('Kullanıcı İşlemleri'),
            ]);
         
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\ApplicationsRelationManager::make(),
            RelationManagers\DocumentsRelationManager::make(),
            RelationManagers\ScholarshipsRelationManager::make(),
        
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
