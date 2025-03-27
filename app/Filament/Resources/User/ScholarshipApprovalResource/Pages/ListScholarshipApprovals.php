<?php

namespace App\Filament\Resources\User\ScholarshipApprovalResource\Pages;

use App\Filament\Resources\User\ScholarshipApprovalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListScholarshipApprovals extends ListRecords
{
    protected static string $resource = ScholarshipApprovalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Burs Onayı')
                ->icon('heroicon-o-user-group'),
        ];
    }

    protected static ?string $title = 'Burs Onayları';

    protected static ?string $breadcrumb = 'Burs Onayları';
}
