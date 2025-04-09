<?php

namespace App\Filament\User\Resources\ProfileResource\Pages;

use App\Filament\User\Resources\ProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditProfile extends EditRecord
{
    protected static string $resource = ProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label('Görüntüle')
                ->icon('heroicon-o-eye'),
            // Actions\DeleteAction::make(),<
        ];
    }
    protected static ?string $breadcrumb = 'Düzenle';

    protected static ?string $breadcrumbParent = 'Profil';

    protected static ?string $title = 'Profil Düzenle';


    public function getTitle(): string
    {
        return 'Profil Düzenle';
    }
    
  protected  function getSavedNotification(): ?Notification
  {
    return Notification::make()
        ->success()
        ->title('Profil başarıyla güncellendi')
        ->body('Profiliniz başarıyla güncellendi.');
  }

  protected function getSaveFormAction(): Actions\Action
  {
      return parent::getSaveFormAction()
          ->label('Kaydet');
  }

  protected function getCancelFormAction(): Actions\Action
  {
      return parent::getCancelFormAction()
          ->label('İptal');
  }

}
