<?php

namespace App\Filament\User\Resources\DocumentUploadResource\Pages;

use App\Filament\User\Resources\DocumentUploadResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDocumentUpload extends CreateRecord
{
    protected static string $resource = DocumentUploadResource::class;

    protected static ?string $title = 'Evrak Yükle';

    protected static ?string $breadcrumb = 'Evrak Yükle';

    protected static ?string $breadcrumbParent = 'Kullanıcı';


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
}
