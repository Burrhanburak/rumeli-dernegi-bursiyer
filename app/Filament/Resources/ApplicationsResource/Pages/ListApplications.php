<?php

namespace App\Filament\Resources\ApplicationsResource\Pages;

use App\Filament\Resources\ApplicationsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApplications extends ListRecords
{
    protected static string $resource = ApplicationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Yeni Başvuru'),
        ];
    }

    protected static ?string $title = 'Başvurular';

    protected static ?string $breadcrumb = 'Başvurular listesi';

    protected static ?string $breadcrumbParent = 'Başvurular';

    protected static ?string $createButtonLabel = 'Yeni Başvuru';

    protected static ?string $createButtonIcon = 'heroicon-o-plus';

    protected static ?string $createButtonColor = 'success';

}
