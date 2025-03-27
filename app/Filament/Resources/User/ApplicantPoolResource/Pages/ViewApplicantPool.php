<?php

namespace App\Filament\Resources\User\ApplicantPoolResource\Pages;

use App\Filament\Resources\User\ApplicantPoolResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewApplicantPool extends ViewRecord
{
    protected static string $resource = ApplicantPoolResource::class;

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

    protected static ?string $title = 'Başvuru Havuzu';

    protected static ?string $breadcrumb = 'Başvuru Havuzu';

    protected static ?string $breadcrumbParent = 'Başvuru Havuzu';
} 