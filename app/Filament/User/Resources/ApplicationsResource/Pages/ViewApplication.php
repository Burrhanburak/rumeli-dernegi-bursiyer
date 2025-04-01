<?php

namespace App\Filament\User\Resources\ApplicationsResource\Pages;

use App\Filament\User\Resources\ApplicationsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewApplication extends ViewRecord
{
    protected static string $resource = ApplicationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make(),
            // Actions\DeleteAction::make(),
            // Add any other actions you need
        ];
    }

    protected static ?string $title = 'Başvuru Görüntüle';
    protected static ?string $breadcrumb = 'Başvuru bilgisi';


}