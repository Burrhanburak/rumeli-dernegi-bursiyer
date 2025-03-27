<?php

namespace App\Filament\User\Resources\ApplicationsResource\Pages;

use App\Filament\User\Resources\ApplicationsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApplications extends EditRecord
{
    protected static string $resource = ApplicationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
