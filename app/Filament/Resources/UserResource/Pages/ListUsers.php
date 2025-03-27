<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Yeni Kullanıcı')
                ->icon('heroicon-o-user-plus'),
        ];
    }

    protected static ?string $title = 'Sistemdeki Kullanıcılar';

    protected static ?string $breadcrumb = 'Kullanıcılar';

    protected static ?string $breadcrumbParent = 'Kullanıcılar';
}
