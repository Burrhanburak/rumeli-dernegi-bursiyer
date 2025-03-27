<?php

namespace App\Filament\Resources\User\DocumentVerificationResource\Pages;

use App\Filament\Resources\User\DocumentVerificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDocumentVerifications extends ListRecords
{
    protected static string $resource = DocumentVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Evrak Doğrulama')
                ->icon('heroicon-o-user-group'),
        ];
    }

    protected static ?string $title = 'Belgelerin Doğrulanması';

    protected static ?string $breadcrumb = 'Belgelerin Doğrulanması';

    protected static ?string $breadcrumbParent = 'Belgelerin Doğrulanması';
}
