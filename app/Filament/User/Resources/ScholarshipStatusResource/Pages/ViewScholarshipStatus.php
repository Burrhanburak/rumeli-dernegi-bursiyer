<?php

namespace App\Filament\User\Resources\ScholarshipStatusResource\Pages;

use App\Filament\User\Resources\ScholarshipStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewScholarshipStatus extends ViewRecord
{
    protected static string $resource = ScholarshipStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make(),
        ];
    }
}
