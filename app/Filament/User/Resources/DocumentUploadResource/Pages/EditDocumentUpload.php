<?php

namespace App\Filament\User\Resources\DocumentUploadResource\Pages;

use App\Filament\User\Resources\DocumentUploadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditDocumentUpload extends EditRecord
{
    protected static string $resource = DocumentUploadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label('Görüntüle')
                ->icon('heroicon-o-eye')
                ->color('info'),
            // Actions\DeleteAction::make(),
        ];
    }

    protected static ?string $title = 'Evrak Düzenle';

    protected static ?string $breadcrumb = 'Evrak Düzenle';

    protected static ?string $breadcrumbParent = 'Kullanıcı';

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Evrak başarıyla güncellendi')
            ->send();
    }
}
