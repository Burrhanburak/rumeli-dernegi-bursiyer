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
use Illuminate\Database\Eloquent\Collection;
use Filament\Notifications\Notification;

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
                Tables\Columns\TextColumn::make('message')
                    ->label('İçerik')
                    ->limit(50)
                    ->searchable(),
                    Tables\Columns\TextColumn::make('type')
                    ->label('Tür')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'document_required' => 'Belge Gerekli',
                        'document_approved' => 'Belge Onaylandı',
                        'document_rejected' => 'Belge Reddedildi',
                        'document_status' => 'Belge Durumu',
                        'interview_scheduled' => 'Mülakat Planlandı',
                        'interview_reminder' => 'Mülakat Hatırlatma',
                        'application_status' => ' Başvuru Durumu',
                        'scholarship_awarded' => 'Burs Verildi',
                        'scholarship_changed' => 'Burs Değişti',
                        'system' => 'Sistem',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'document_required' => 'danger',
                        'document_approved' => 'success',
                        'document_rejected' => 'danger',
                        'document_status' => 'warning',
                        'interview_scheduled' => 'info',
                        'interview_reminder' => 'warning',
                        'application_status' => 'danger',
                        'scholarship_awarded' => 'success',
                        'scholarship_changed' => 'warning',
                        'system' => 'secondary',
                        default => 'secondary',
                    })
                    ->sortable(),
                    
                Tables\Columns\IconColumn::make('is_read')
                    ->label('Okundu')
                    ->boolean()
                    ->default(true)
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Gönderilme Tarihi')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('read_at')
                    ->label('Okunma Tarihi')
                    ->dateTime('d.m.Y H:i:s')
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
                        'document_required' => 'Belge Gerekli',
                        'document_approved' => 'Belge Onaylandı',  
                        'document_rejected' => 'Belge Reddedildi',
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
                            
                            // Debug için bilgi eklendi
                            \Illuminate\Support\Facades\Log::info('Notification marked as read', [
                                'id' => $record->id,
                                'read_at' => $record->read_at,
                                'is_read' => $record->is_read
                            ]);
                        }
                    })
                    ->visible(fn (Notifications $record): bool => !$record->is_read),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_all_read')
                        ->label('Tümünü Okundu İşaretle')
                        ->icon('heroicon-o-check')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                if (!$record->is_read) {
                                    $record->is_read = true;
                                    $record->read_at = now();
                                    $record->save();
                                }
                            });
                            
                            Notification::make()
                                ->title('Tüm seçili bildirimler okundu olarak işaretlendi')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
