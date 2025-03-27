<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivitiyLogsResource\Pages;
use App\Filament\Resources\ActivitiyLogsResource\RelationManagers;
use App\Models\ActivityLogs;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ActivitiyLogsResource extends Resource
{
    protected static ?string $model = ActivityLogs::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    
    protected static ?string $navigationGroup = 'Sistem';
    
    protected static ?int $navigationSort = 1;
    
    protected static ?string $navigationLabel = 'Aktivite Kayıtları';

    protected static ?string $title = 'Etkinlik Kayıtları';

    protected static ?string $breadcrumb = 'Etkinlik Kayıtları';

    protected static ?string $breadcrumbParent = 'Etkinlik Kayıtları';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Kayıt Bilgileri')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->placeholder('Seçiniz...')
                            ->label('Kullanıcı')
                            ->searchable(),
                        Forms\Components\TextInput::make('action')
                            ->label('İşlem')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('entity_type')
                            ->label('Varlık Türü')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('entity_id')
                            ->label('Varlık ID')
                            ->numeric(),
                        Forms\Components\Textarea::make('description')
                            ->label('Açıklama')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('old_values')
                            ->label('Eski Değerler')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('new_values')
                            ->label('Yeni Değerler')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('ip_address')
                            ->label('IP Adresi')
                            ->maxLength(45),
                        Forms\Components\TextInput::make('user_agent')
                            ->label('Kullanıcı Tarayıcısı')
                            ->maxLength(255),
                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Oluşturulma Tarihi')
                            ->required()
                            ->default(now()),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Etkinlik kayıtları bulunamadı')
            ->emptyStateDescription('Yeni bir etkinlik kaydı oluşturmak için "Yeni Etkinlik Kaydı" düğmesine tıklayın.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Yeni Etkinlik Kaydı')
                    ->icon('heroicon-o-plus')
                    ->color('success')
            ])
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Kullanıcı')
                    ->searchable()
                    ->default('Sistem'),
                Tables\Columns\TextColumn::make('action')
                    ->label('İşlem')
                    ->searchable(),
                Tables\Columns\TextColumn::make('entity_type')
                    ->label('Varlık Türü')
                    ->searchable(),
                Tables\Columns\TextColumn::make('entity_id')
                    ->label('Varlık ID')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Adresi')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma Tarihi')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('action')
                    ->label('İşlem')
                    ->options([
                        'created' => 'Oluşturuldu',
                        'updated' => 'Güncellendi',
                        'deleted' => 'Silindi',
                        'logged_in' => 'Giriş Yapıldı',
                        'logged_out' => 'Çıkış Yapıldı',
                        'failed_login' => 'Başarısız Giriş',
                        'uploaded' => 'Yüklendi',
                        'downloaded' => 'İndirildi',
                        'submitted' => 'Gönderildi',
                        'approved' => 'Onaylandı',
                        'rejected' => 'Reddedildi',
                    ]),
                Tables\Filters\SelectFilter::make('entity_type')
                    ->label('Varlık Türü')
                    ->options([
                        'user' => 'Kullanıcı',
                        'scholarship' => 'Burs',
                        'application' => 'Başvuru',
                        'document' => 'Belge',
                        'program' => 'Program',
                        'interview' => 'Mülakat',
                        'notification' => 'Bildirim',
                        'payment' => 'Ödeme',
                    ]),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivitiyLogs::route('/'),
            'create' => Pages\CreateActivitiyLogs::route('/create'),
            'view' => Pages\ViewActivitiyLogs::route('/{record}'),
            'edit' => Pages\EditActivitiyLogs::route('/{record}/edit'),
        ];
    }
}
