<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationsResource\Pages;
use App\Filament\Resources\NotificationsResource\RelationManagers;
use App\Models\Notifications;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NotificationsResource extends Resource
{
    protected static ?string $model = Notifications::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell';
    
    protected static ?string $navigationGroup = 'İletişim';
    
    protected static ?int $navigationSort = 1;
    
    protected static ?string $navigationLabel = 'Bildirimler';

    protected static ?string $title = 'Bildirimler';

    protected static ?string $breadcrumb = 'Bildirim';

    protected static ?string $breadcrumbParent = 'İletişim';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Bildirim Bilgileri')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('Kullanıcı')
                            ->required()
                            ->searchable(),
                        Forms\Components\TextInput::make('title')
                            ->label('Başlık')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('content')
                            ->label('İçerik')
                            ->required()
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('type')
                            ->label('Tür')
                            ->options([
                                'system' => 'Sistem',
                                'application' => 'Başvuru',
                                'scholarship' => 'Burs',
                                'interview' => 'Mülakat',
                                'document' => 'Belge',
                                'payment' => 'Ödeme',
                            ])
                            ->required(),
                        Forms\Components\Toggle::make('is_read')
                            ->label('Okundu mu')
                            ->required()
                            ->default(false),
                        Forms\Components\DateTimePicker::make('read_at')
                            ->label('Okunma Tarihi'),
                        Forms\Components\DateTimePicker::make('sent_at')
                            ->label('Gönderilme Tarihi')
                            ->required()
                            ->default(now()),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->emptyStateHeading('Bildirim bulunamadı')
        ->emptyStateDescription('Yeni bir bildirim oluşturmak için "Yeni Bildirim" düğmesine tıklayın.')
        ->emptyStateActions([
            Tables\Actions\CreateAction::make()
                ->label('Yeni Bildirim')
                ->icon('heroicon-o-bell')
        ])
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Kullanıcı')
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tür')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'system' => 'Sistem',
                        'application' => 'Başvuru',
                        'scholarship' => 'Burs',
                        'interview' => 'Mülakat',
                        'document' => 'Belge',
                        'payment' => 'Ödeme',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_read')
                    ->label('Okundu')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sent_at')
                    ->label('Gönderilme Tarihi')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('read_at')
                    ->label('Okunma Tarihi')
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
                Tables\Filters\SelectFilter::make('is_read')
                    ->label('Okunma Durumu')
                    ->options([
                        true => 'Okundu',
                        false => 'Okunmadı',
                    ]),
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tür')
                    ->options([
                        'system' => 'Sistem',
                        'application' => 'Başvuru',
                        'scholarship' => 'Burs',
                        'interview' => 'Mülakat',
                        'document' => 'Belge',
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
            'index' => Pages\ListNotifications::route('/'),
            'create' => Pages\CreateNotifications::route('/create'),
            'view' => Pages\ViewNotifications::route('/{record}'),
            'edit' => Pages\EditNotifications::route('/{record}/edit'),
        ];
    }
}
