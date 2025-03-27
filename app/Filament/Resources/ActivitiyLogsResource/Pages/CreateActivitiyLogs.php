<?php

namespace App\Filament\Resources\ActivitiyLogsResource\Pages;

use App\Filament\Resources\ActivitiyLogsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateActivitiyLogs extends CreateRecord
{
    protected static string $resource = ActivitiyLogsResource::class;

    protected static ?string $title = 'Yeni Etkinlik Kaydı';

    protected static ?string $breadcrumb = 'Yeni Etkinlik Kaydı';

    protected static ?string $breadcrumbParent = 'Etkinlik Kayıtları';

}
