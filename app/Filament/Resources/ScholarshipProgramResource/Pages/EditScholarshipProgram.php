<?php

namespace App\Filament\Resources\ScholarshipProgramResource\Pages;

use App\Filament\Resources\ScholarshipProgramResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditScholarshipProgram extends EditRecord
{
    protected static string $resource = ScholarshipProgramResource::class;

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

    protected static ?string $title = 'Burs Programını Düzenle';

    protected static ?string $breadcrumb = 'Burs Programları';

    protected static ?string $breadcrumbParent = 'Programlar';

    public function getTitle(): string
    {
        return 'Burs Programını Düzenle';
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->label('Kaydet')
            ->color('success');
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->label('İptal')
            
            ->color('danger');
    }


    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Burs Programı Düzenlendi')
            ->body('Burs programı başarıyla düzenlendi.')
            ->success();
    }
    
}
