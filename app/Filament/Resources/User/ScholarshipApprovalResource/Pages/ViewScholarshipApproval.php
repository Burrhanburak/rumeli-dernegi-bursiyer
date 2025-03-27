<?php

namespace App\Filament\Resources\User\ScholarshipApprovalResource\Pages;

use App\Filament\Resources\User\ScholarshipApprovalResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewScholarshipApproval extends ViewRecord
{
    protected static string $resource = ScholarshipApprovalResource::class;

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

    protected static ?string $title = 'Burs Kabulü';

    protected static ?string $breadcrumb = 'Burs Kabulü';

    protected static ?string $breadcrumbParent = 'Burs Kabulü';
} 