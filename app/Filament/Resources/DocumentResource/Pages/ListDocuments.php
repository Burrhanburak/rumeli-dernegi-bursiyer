<?php

namespace App\Filament\Resources\DocumentResource\Pages;

use App\Filament\Resources\DocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class ListDocuments extends ListRecords
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Yeni Belgeler')
                ->icon('heroicon-o-document-plus')
                
        ];
    }

    protected static ?string $title = 'Belgeler';

    protected static ?string $breadcrumb = 'Belgeler listesi';

    protected static ?string $breadcrumbParent = 'Belgeler';

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Belge başarıyla oluşturuldu');
    }

    protected function getDeletedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Belge başarıyla silindi');
    }

    protected function getUpdatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Belge başarıyla güncellendi');
    }



    
}
