<?php

namespace App\Filament\User\Resources\ScholarshipStatusResource\Pages;

use App\Filament\User\Resources\ScholarshipStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewScholarshipStatus extends ViewRecord
{
    protected static string $resource = ScholarshipStatusResource::class;

    protected static ?string $title = 'Burs Detayları';

    protected static ?string $breadcrumb = 'Burs Detayları';

    protected function getHeaderActions(): array
    {
        return [
            // Bursu düzenleme izni olmadığı için herhangi bir eylem yok
        ];
    }
}
