<?php

namespace App\Filament\Resources\ProgramDocumentRequirementResource\Pages;

use App\Filament\Resources\ProgramDocumentRequirementResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProgramDocumentRequirement extends ViewRecord
{
    protected static string $resource = ProgramDocumentRequirementResource::class;

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
