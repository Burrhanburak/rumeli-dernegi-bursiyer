<?php

namespace App\Filament\Resources\DocumentTypeResource\Pages;

use App\Filament\Resources\DocumentTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
class CreateDocumentType extends CreateRecord
{
    protected static string $resource = DocumentTypeResource::class;

    protected static ?string $title = 'Yeni Belge Türü';

    protected static ?string $breadcrumb = 'Yeni Belge Türü';

    protected static ?string $breadcrumbParent = 'Belge Türleri';
    
    protected function getCreateFormAction(): Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Oluştur');
    }

    protected function getCreateAnotherFormAction(): Actions\Action
    {
        return parent::getCreateAnotherFormAction()
            ->label('Oluştur ve başka ekle');
    }

    protected function getCancelFormAction(): Actions\Action
    {
        return parent::getCancelFormAction()
            ->label('İptal');
    }

    protected function getSaveFormAction(): Actions\Action
    {
        return parent::getSaveFormAction()
            ->label('Kaydet');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Belge Türü Oluşturuldu')
            ->success()
            ->send();
    }
}