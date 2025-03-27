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

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationGroup = 'Kullanıcı Yönetimi';
    
    protected static ?int $navigationSort = 1;
    
    protected static ?string $navigationLabel = 'Kullanıcılar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Kullanıcı Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('ad')
                            ->label('Ad')
                            ->placeholder('Adınızı girin')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('soyad')
                            ->label('Soyad')
                            ->placeholder('Soyadınızı girin')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('E-posta')
                            ->placeholder('E-posta adresinizi girin')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\DateTimePicker::make('email_verified_at')
                            ->label('E-posta Doğrulama Tarihi'),
                        Forms\Components\TextInput::make('password')
                            ->label('Şifre')
                            ->password()
                            ->placeholder('Şifre oluşturun')
                            ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create'),
                        Forms\Components\Toggle::make('is_admin')
                            ->label('Yönetici Yetkisi')
                            ->helperText('Kullanıcıya yönetici yetkisi verir')
                            ->onColor('success')
                            ->offColor('danger'),
                        Forms\Components\TextInput::make('phone')
                            ->label('Telefon')
                            ->tel()
                            ->placeholder('Örnek: 0555 555 55 55')
                            ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                            ->maxLength(20),
                        Forms\Components\TextInput::make('address')
                            ->label('Adres')
                            ->placeholder('Adresinizi girin')
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('birth_date')
                            ->label('Doğum Tarihi'),
                        Forms\Components\FileUpload::make('image')
                            ->label('Profil Fotoğrafı')
                            ->image()
                            ->imageEditor()
                            ->circleCropper()
                            ->directory('user-images')
                            ->visibility('public')
                            ->downloadable()
                            ->placeholder('Profil resmi yükleyin veya sürükleyin')
                            ->imagePreviewHeight('250')
                            ->loadingIndicatorPosition('left')
                            ->panelAspectRatio('2:1')
                            ->imageResizeMode('cover')
                            ->panelLayout('integrated')
                            ->disk('public'),
                    ])->columns(2),
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
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Sil'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ApplicationsRelationManager::make(),
            RelationManagers\DocumentsRelationManager::make(),
            RelationManagers\ScholarshipsRelationManager::make(),
            RelationManagers\NotificationsRelationManager::make(),
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
