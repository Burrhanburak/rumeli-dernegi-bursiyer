<?php

namespace App\Filament\Resources\InterviewResource\Pages;

use App\Filament\Resources\InterviewResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditInterview extends EditRecord
{
    protected static string $resource = InterviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label('Görüntüle'),
            Actions\DeleteAction::make()
                ->label('Sil'),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Görüşme başarıyla kaydedildi');
    }   

    protected function getDeletedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Görüşme başarıyla silindi');
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
}