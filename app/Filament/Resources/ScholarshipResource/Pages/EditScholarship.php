<?php

namespace App\Filament\Resources\ScholarshipResource\Pages;

use App\Filament\Resources\ScholarshipResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditScholarship extends EditRecord
{
    protected static string $resource = ScholarshipResource::class;

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
