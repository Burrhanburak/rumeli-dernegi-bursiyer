<?php

namespace App\Filament\Resources\User\ScholarshipStudentsResource\Pages;

use App\Filament\Resources\User\ScholarshipStudentsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

class EditScholarshipStudent extends EditRecord
{
    protected static string $resource = ScholarshipStudentsResource::class;

    protected static ?string $title = 'Burslu Öğrenci Düzenle';

    protected static ?string $breadcrumb = 'Burslu Öğrenci Düzenle';
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
            ->label('Görüntüle'),
            Actions\DeleteAction::make()
            ->label('Sil')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Öğrenciyi Sil')
            ->modalDescription('Bu işlem geri alınamaz. Öğrenciyi silmek istediğinize emin misiniz?')
            ->modalSubmitActionLabel('Evet, Sil')
            ->modalCancelActionLabel('İptal'),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
    
    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Burslu Öğrenci Düzenlendi')
            ->body('Burslu öğrenci başarıyla düzenlendi.')
            ->success();
    }


    protected function getSaveFormAction(): Actions\Action
    {
        return parent::getSaveFormAction()
            ->label('Değişlikleri Kaydet');
    }

    protected function getCancelFormAction(): Actions\Action
    {
        return parent::getCancelFormAction()
            ->label('İptal');
    }

    
    
    
} 