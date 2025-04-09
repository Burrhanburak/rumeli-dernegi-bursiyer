<?php

namespace App\Filament\Resources\ApplicationsResource\Pages;

use App\Filament\Resources\ApplicationsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateApplications extends CreateRecord
{
    protected static string $resource = ApplicationsResource::class;

    public function save(): void
    {
        Notification::make()
            ->title('Başvuru Oluşturuldu')
            ->success()
            ->send();
    }

    protected static ?string $title = 'Yeni Başvuru';

    protected static ?string $breadcrumb = 'Başvurular';

    protected static ?string $breadcrumbParent = 'Yeni Başvuru';

    protected static ?string $createButtonIcon = 'heroicon-o-plus';

    protected static ?string $createButtonColor = 'success';

    protected static ?string $createButtonTooltip = 'Yeni Başvuru Oluştur';

    protected static ?string $createButtonAriaLabel = 'Yeni Başvuru Oluştur';

    protected static ?string $createButtonLabel = 'Yeni Başvuru';


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
            ->title('Başvuru Oluşturuldu')
            ->success()
            ->send();
    }

}
