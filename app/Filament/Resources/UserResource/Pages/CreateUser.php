<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected static ?string $title = 'Yeni Kullanıcı';

    protected static ?string $breadcrumb = 'Kullanıcılar';

    protected static ?string $breadcrumbParent = 'Kullanıcılar';


    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Kullanıcı Oluşturuldu')
            ->body('Kullanıcı başarıyla oluşturuldu.')
            ->success();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
