<?php

namespace App\Filament\Resources\DocumentResource\Pages;

use App\Filament\Resources\DocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateDocument extends CreateRecord
{
    protected static string $resource = DocumentResource::class;

    protected static ?string $title = 'Yeni Belgeler';

    protected static ?string $breadcrumb = 'Yeni Belgeler';

    protected static ?string $breadcrumbParent = 'Belgeler';



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

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }



    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Belge Oluşturuldu')
            ->body('Belge başarıyla oluşturuldu.')
            ->success();
    }
    

}
