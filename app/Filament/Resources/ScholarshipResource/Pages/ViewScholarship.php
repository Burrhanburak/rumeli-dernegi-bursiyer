<?php

namespace App\Filament\Resources\ScholarshipResource\Pages;

use App\Filament\Resources\ScholarshipResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewScholarship extends ViewRecord
{
    protected static string $resource = ScholarshipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Düzenle')
                ->icon('heroicon-o-pencil')
                ->color('warning'),
            Actions\DeleteAction::make()
                ->label('Sil')
                ->icon('heroicon-o-trash')
                ->color('danger'),
        ];
    }
}
