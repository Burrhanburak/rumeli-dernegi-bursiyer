<?php

namespace App\Filament\User\Resources\DocumentUploadResource\Pages;

use App\Filament\User\Resources\DocumentUploadResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateDocumentUpload extends CreateRecord
{
    protected static string $resource = DocumentUploadResource::class;

    protected static ?string $title = 'Evrak Yükle';

    protected static ?string $breadcrumb = 'Evrak Yükle';

    protected static ?string $breadcrumbParent = 'Kullanıcı';


    protected function getCreateFormAction(): Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Oluştur')
            ->successNotification(
                Notification::make()
                    ->title('Belge başarıyla yüklendi')
                    ->body('Belgeniz başarıyla yüklendi.')
                    ->success()
            );
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

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Belge başarıyla yüklendi')
            ->body('Belgeniz başarıyla yüklendi.')
            ->send();
    }

    
}
