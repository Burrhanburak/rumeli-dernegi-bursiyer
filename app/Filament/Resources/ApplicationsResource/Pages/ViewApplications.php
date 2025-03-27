<?php

namespace App\Filament\Resources\ApplicationsResource\Pages;

use App\Filament\Resources\ApplicationsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewApplications extends ViewRecord
{
    protected static string $resource = ApplicationsResource::class;

    protected static ?string $title = 'Başvuru Detayı';

    protected static ?string $breadcrumb = 'Görüntüle';

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
    
}
