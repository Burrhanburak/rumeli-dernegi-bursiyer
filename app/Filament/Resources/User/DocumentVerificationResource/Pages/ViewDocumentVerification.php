<?php

namespace App\Filament\Resources\User\DocumentVerificationResource\Pages;

use App\Filament\Resources\User\DocumentVerificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDocumentVerification extends ViewRecord
{
    protected static string $resource = DocumentVerificationResource::class;

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

    protected static ?string $title = 'Belgelerin Doğrulanması';

    protected static ?string $breadcrumb = 'Belgelerin Doğrulanması';

    protected static ?string $breadcrumbParent = 'Belgelerin Doğrulanması';
} 