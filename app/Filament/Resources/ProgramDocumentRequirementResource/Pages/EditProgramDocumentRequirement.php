<?php

namespace App\Filament\Resources\ProgramDocumentRequirementResource\Pages;

use App\Filament\Resources\ProgramDocumentRequirementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProgramDocumentRequirement extends EditRecord
{
    protected static string $resource = ProgramDocumentRequirementResource::class;

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
}
