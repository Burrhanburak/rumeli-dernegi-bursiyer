<?php

namespace App\Filament\User\Resources\ApplicationsResource\Pages;

use App\Filament\User\Resources\ApplicationsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;


class EditApplications extends EditRecord
{
    protected static string $resource = ApplicationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }

    protected static ?string $title = 'Başvuru Düzenle';
    protected static ?string $breadcrumb = 'Başvuru Düzenle';



   
    protected function getEditFormAction(): Actions\Action
    {
        return parent::getEditFormAction()
            ->label('Güncelle');
    }
    
    protected function getSaveFormAction(): Actions\Action
    {
        return parent::getSaveFormAction()
            ->label('Değişiklikleri Kaydet');
    }

    protected function getCancelFormAction(): Actions\Action
    {
        return parent::getCancelFormAction()
            ->label('İptal');
         
    }
}
