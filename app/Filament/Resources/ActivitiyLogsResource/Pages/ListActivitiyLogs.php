<?php

namespace App\Filament\Resources\ActivitiyLogsResource\Pages;

use App\Filament\Resources\ActivitiyLogsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListActivitiyLogs extends ListRecords
{
    protected static string $resource = ActivitiyLogsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Yeni Etkinlik Kaydı')
                ->icon('heroicon-o-plus')
                ->color('success')
               
        ];
    }

    protected static ?string $title = 'Etkinlik Kayıtları';

    protected static ?string $breadcrumb = 'Etkinlik Kayıtları listesi';

    protected static ?string $breadcrumbParent = 'Etkinlik Kayıtları listesi';

    protected static ?string $createButtonLabel = 'Yeni Etkinlik Kaydı';

    protected static ?string $createButtonIcon = 'heroicon-o-plus';

    protected static ?string $createButtonColor = 'success';

}
