<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label('Görüntüle')
                ->icon('heroicon-o-eye')
                ->color('info'),
            Actions\DeleteAction::make()
                ->label('Sil')
                ->icon('heroicon-o-trash')
                ->color('danger'),
        ];
    }

    protected static ?string $title = 'Kullanıcı Düzenle';

    protected static ?string $breadcrumb = 'Kullanıcı Düzenle';

    protected static ?string $breadcrumbParent = 'Kullanıcılar';
    

    protected function getSaveFormAction(): Actions\Action
    {
        return parent::getSaveFormAction()
            ->label('Kaydet');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Kullanıcı Düzenlendi')
            ->body('Kullanıcı başarıyla düzenlendi.')
            ->success();
    }
}
