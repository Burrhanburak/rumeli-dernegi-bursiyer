<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\ProfileResource\Pages;
use App\Filament\User\Resources\ProfileResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;
use Ysfkaya\FilamentPhoneInput\Infolists\PhoneEntry;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;


class ProfileResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    
    protected static ?string $navigationLabel = 'Profilim';
    
    protected static ?int $navigationSort = 6;

    protected static ?string $navigationGroup = 'Hesap Ayarları';

    protected static ?string $title = 'Profilim';

    protected static ?string $breadcrumb = 'Profil';

    protected static ?string $breadcrumbParent = 'Kullanıcı';
    

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('id', Auth::id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Kişisel Bilgiler')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Adı')
                            ->placeholder('Adınızı giriniz')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('surname')
                            ->label('Soyadı')
                            ->placeholder('Soyadınızı giriniz')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('E-posta')
                            ->placeholder('E-postanızı giriniz')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        
                            PhoneInput::make('phone')
            ->defaultCountry('tr')
            ->initialCountry('tr')
            ->placeholder('Telefon numaranızı giriniz')
            ->locale('tr')
            ->countrySearch(false)
            ->label('Telefon Numarası')
            ->required()
            ->unique(User::class)
            ->validationMessages([
                'unique' => 'Bu telefon numarası zaten kayıtlı.'
            ]),
                        Forms\Components\DatePicker::make('birth_date')
                            ->label('Doğum Tarihi')
                            ->displayFormat('d/m/Y')
                            ->placeholder('Doğum tarihinizi giriniz')
                            ->maxDate(now()->subYears(17)),
                        Forms\Components\FileUpload::make('image')
                            ->label('Profil Fotoğrafı')
                            ->image()
                            ->directory('profile-photos')
                            ->visibility('public')
                            ->maxSize(1024) // 1MB
                            ->placeholder('Profil fotoğrafınızı Değiştiriniz')
                            ->uploadingMessage(
                                'Yükleniyor...'
                            )
                            ->circleCropper()
                            ->openable()
                            ->reorderable(false)
                            ->avatar()
                            
                           
                      
                    ])->columns(2),
                
                Forms\Components\Section::make('Adres Bilgileri')
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
                    ])->columns(2),
                
                Forms\Components\Section::make('Şifre Değiştir')
                    ->schema([
                        Forms\Components\TextInput::make('current_password')
                            ->label('Mevcut Şifre')
                            ->placeholder('Mevcut şifrenizi giriniz')
                            ->password()
                            ->dehydrated(false)
                            ->rule('current_password'),
                        Forms\Components\TextInput::make('password')
                            ->label('Yeni Şifre')
                            ->placeholder('Yeni şifrenizi giriniz')
                            ->password()
                            ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->rule('confirmed'),
                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Yeni Şifre Tekrar')
                            ->placeholder('Yeni şifrenizi tekrar giriniz')
                            ->password()
                            ->dehydrated(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Fotoğraf')
                    ->circular()
                    ->defaultImageUrl(function ($record) {
                        $name = trim(($record->name ?? '') . ' ' . ($record->surname ?? ''));
                        if (empty($name)) $name = 'Kullanıcı';
                        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&size=128&background=808080&color=ffffff&bold=true&length=2';
                    }),
                Tables\Columns\TextColumn::make('name')
                    ->label('Ad Soyad')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('birth_date')
                    ->label('Doğum Tarihi')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Düzenle'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProfiles::route('/'),
            'edit' => Pages\EditProfile::route('/{record}/edit'),
            'view' => Pages\ViewProfile::route('/{record}'),
        ];
    }
}
