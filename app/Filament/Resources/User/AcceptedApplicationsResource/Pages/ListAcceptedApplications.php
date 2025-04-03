<?php

namespace App\Filament\Resources\User\AcceptedApplicationsResource\Pages;

use App\Filament\Resources\User\AcceptedApplicationsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAcceptedApplications extends ListRecords
{
    protected static string $resource = AcceptedApplicationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    protected static ?string $title = 'Onaylanan Başvurular';
}
