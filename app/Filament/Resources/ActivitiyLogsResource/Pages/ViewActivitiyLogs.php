<?php

namespace App\Filament\Resources\ActivitiyLogsResource\Pages;

use App\Filament\Resources\ActivitiyLogsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewActivitiyLogs extends ViewRecord
{
    protected static string $resource = ActivitiyLogsResource::class;

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

    protected static ?string $title = 'Etkinlik Kaydı';

    protected static ?string $breadcrumb = 'Etkinlik Kaydı';

    protected static ?string $breadcrumbParent = 'Etkinlik Kayıtları';
    
    
}
