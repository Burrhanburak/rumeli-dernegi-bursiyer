<?php

namespace App\Filament\User\Resources\ScholarshipStatusResource\Pages;

use App\Filament\User\Resources\ScholarshipStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditScholarshipStatus extends EditRecord
{
    protected static string $resource = ScholarshipStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            // Actions\DeleteAction::make(),
        ];
    }
}
