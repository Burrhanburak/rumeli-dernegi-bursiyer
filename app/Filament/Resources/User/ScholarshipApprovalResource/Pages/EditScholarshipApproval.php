<?php

namespace App\Filament\Resources\User\ScholarshipApprovalResource\Pages;

use App\Filament\Resources\User\ScholarshipApprovalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditScholarshipApproval extends EditRecord
{
    protected static string $resource = ScholarshipApprovalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Sil')
                ->icon('heroicon-o-trash')
                ->color('danger'),
        ];
    }

    protected static ?string $title = 'Burs Kabulü Düzenle';

    protected static ?string $breadcrumb = 'Burs Kabulü';

    protected static ?string $breadcrumbParent = 'Burs Kabulü';
}
