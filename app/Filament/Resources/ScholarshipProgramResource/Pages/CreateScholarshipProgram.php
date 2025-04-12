<?php

namespace App\Filament\Resources\ScholarshipProgramResource\Pages;

use App\Filament\Resources\ScholarshipProgramResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateScholarshipProgram extends CreateRecord
{
    protected static string $resource = ScholarshipProgramResource::class;

    protected static ?string $title = 'Yeni Program';

    protected static ?string $breadcrumb = 'Programlar';

    protected static ?string $breadcrumbParent = 'Programlar';


    protected static ?string $createButtonLabel = 'Yeni Program';

    protected static ?string $createButtonIcon = 'heroicon-o-plus';

    protected static ?string $createButtonColor = 'success';

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
            ->title('Program Oluşturuldu')
            ->body('Program başarıyla oluşturuldu.')
            ->success();
    }
}
