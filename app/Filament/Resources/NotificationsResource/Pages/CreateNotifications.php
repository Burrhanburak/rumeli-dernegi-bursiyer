<?php

namespace App\Filament\Resources\NotificationsResource\Pages;

use App\Filament\Resources\NotificationsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateNotifications extends CreateRecord
{
    protected static string $resource = NotificationsResource::class;

    protected static ?string $title = 'Yeni Bildirim';

    protected static ?string $breadcrumb = 'Bildirimler';

    protected static ?string $breadcrumbParent = 'Bildirimler';

    protected static ?string $createButtonIcon = 'heroicon-o-bell';

    protected static ?string $createButtonColor = 'success';

    protected static ?string $createButtonTooltip = 'Yeni Bildirim Oluştur';

    protected static ?string $createButtonAriaLabel = 'Yeni Bildirim Oluştur';
    
}
