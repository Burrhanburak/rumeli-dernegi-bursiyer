<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\NotificationResource\Pages;
use App\Filament\User\Resources\NotificationResource\RelationManagers;
use App\Models\Notifications;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class NotificationResource extends Resource
{
    protected static ?string $model = Notifications::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell';
    
    protected static ?string $navigationLabel = 'Bildirimlerim';
    
    protected static ?int $navigationSort = 5;
    
    protected static ?string $navigationGroup = 'Hesap Ayarları';

    protected static ?string $title = 'Bildirimler';

    protected static ?string $breadcrumb = 'Bildirim';

    protected static ?string $breadcrumbParent = 'İletişim';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', Auth::id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Sadece görüntüleme
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
          ->emptyStateHeading('Bildirim bulunamadı')
          ->emptyStateDescription('Güncel Herhangi bir bildirim bulunamadı.')
          
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable(),
                Tables\Columns\TextColumn::make('content')
                    ->label('İçerik')
                    ->limit(50)
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
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sent_at')
                    ->label('Gönderilme Tarihi')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('read_at')
                    ->label('Okunma Tarihi')
                    ->dateTime()
                    ->sortable(),
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
                    ->label('Görüntüle')
                    ->mutateRecordDataUsing(function (array $data): array {
                        // Görüntülendiğinde okundu olarak işaretle
                        $notification = Notifications::find($data['id']);
                        if ($notification && !$notification->is_read) {
                            $notification->is_read = true;
                            $notification->read_at = now();
                            $notification->save();
                        }
                        return $data;
                    }),
                Tables\Actions\Action::make('mark_read')
                    ->label('Okundu İşaretle')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->action(function (Notifications $record) {
                        if (!$record->is_read) {
                            $record->is_read = true;
                            $record->read_at = now();
                            $record->save();
                        }
                    })
                    ->visible(fn (Notifications $record): bool => !$record->is_read),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('mark_all_read')
                    ->label('Tümünü Okundu İşaretle')
                    ->icon('heroicon-o-check')
                    ->action(function ($records) {
                        foreach ($records as $record) {
                            if (!$record->is_read) {
                                $record->is_read = true;
                                $record->read_at = now();
                                $record->save();
                            }
                        }
                    }),
            ])
            ->defaultSort('sent_at', 'desc');
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
            'view' => Pages\ViewNotification::route('/{record}'),
        ];
    }
}
