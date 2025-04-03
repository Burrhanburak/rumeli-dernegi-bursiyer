<?php

namespace App\Filament\Resources\User\RejectedApplicationsResource\Pages;

use App\Filament\Resources\User\RejectedApplicationsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRejectedApplications extends ListRecords
{
    protected static string $resource = RejectedApplicationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // İhtiyaca göre header aksiyonları eklenebilir
        ];
    }

    protected static ?string $title = 'Reddedilen Başvurular';

    protected static ?string $breadcrumb = 'Reddedilen Başvurular';
} 