<?php

namespace App\Filament\Resources\ProgramDocumentRequirementResource\Pages;

use App\Filament\Resources\ProgramDocumentRequirementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProgramDocumentRequirements extends ListRecords
{
    protected static string $resource = ProgramDocumentRequirementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Program Belge Gereksinimi')
                ->icon('heroicon-o-clipboard-document-check'),
        ];
    }

    protected static ?string $title = 'Program Gereklilik Belgeleri';

    protected static ?string $breadcrumb = 'Program Belgeleri';

    protected static ?string $breadcrumbParent = 'Program Gereklilik Belgeleri';
}
