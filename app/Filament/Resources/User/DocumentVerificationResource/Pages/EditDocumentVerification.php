<?php

namespace App\Filament\Resources\User\DocumentVerificationResource\Pages;

use App\Filament\Resources\User\DocumentVerificationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDocumentVerification extends EditRecord
{
    protected static string $resource = DocumentVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Sil')
                ->icon('heroicon-o-trash')
                ->color('danger'),
        ];
    }
}
