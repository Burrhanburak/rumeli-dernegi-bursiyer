<?php

namespace App\Filament\Resources\User\AcceptedApplicationsResource\Pages;

use App\Filament\Resources\User\AcceptedApplicationsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAcceptedApplications extends EditRecord
{
    protected static string $resource = AcceptedApplicationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }


    public function getTitle(): string
    {
        return 'Başvuru Düzenle';
    }

    protected function getSaveAction(): Action
    {
        return Action::make('save')
            ->label('Kaydet')
            ->icon('heroicon-o-save')
            ->color('success');
    }
}
