<?php

namespace App\Filament\User\Resources\NotificationResource\Pages;

use App\Filament\User\Resources\NotificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewNotification extends ViewRecord
{
    protected static string $resource = NotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make(),
        ];
    }

    protected static ?string $title = 'Bildirim Görüntüle';

    protected static ?string $breadcrumb = 'Bildirimler';

    protected static ?string $breadcrumbParent = 'Bildirimler';
    
    public function mount(int|string $record): void
    {
        parent::mount($record);
        
        $notification = $this->record;
        
        // Bildirim okunmadıysa, okundu olarak işaretle
        if (!$notification->is_read) {
            $notification->is_read = true;
            $notification->read_at = now();
            $notification->save();
            
            // Sayfayı yenile
            $this->redirect(ViewNotification::getUrl(['record' => $record]));
        }
    }
    
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Bildirim Detayları')
                    ->schema([
                        Infolists\Components\TextEntry::make('title')
                            ->label('Başlık')
                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                            ->weight('bold'),
                        Infolists\Components\TextEntry::make('message')
                            ->label('İçerik')
                            ->markdown()
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('type')
                            ->label('Tür')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'document_required' => 'Belge - Belge Gerekli',
                                'document_approved' => 'Belge - Belge Onaylandı',
                                'document_rejected' => 'Belge - Belge Reddedildi',
                                'interview_scheduled' => 'Mülakat - Mülakat Planlandı',
                                'interview_reminder' => 'Mülakat - Mülakat Hatırlatma',
                                'application_status' => 'Başvuru - Başvuru Durumu',
                                'scholarship_awarded' => 'Burs - Burs Verildi',
                                'scholarship_changed' => 'Burs - Burs Değişti',
                                'system' => 'Sistem',
                                default => $state,
                            }),
                        Infolists\Components\IconEntry::make('is_read')
                            ->label('Okundu Durumu')
                            ->boolean(),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Gönderilme Tarihi')
                            ->dateTime('d.m.Y H:i:s'),
                        Infolists\Components\TextEntry::make('read_at')
                            ->label('Okunma Tarihi')
                            ->default(now())
                            ->dateTime('d.m.Y H:i:s'),
                    ])
                    ->columns(2),
            ]);
    }
}
