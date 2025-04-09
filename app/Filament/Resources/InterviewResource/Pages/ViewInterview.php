<?php

namespace App\Filament\Resources\InterviewResource\Pages;

use App\Filament\Resources\InterviewResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;
class ViewInterview extends ViewRecord
{
    protected static string $resource = InterviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
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

    protected function getUpdatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Görüşme başarıyla güncellendi');
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
}
