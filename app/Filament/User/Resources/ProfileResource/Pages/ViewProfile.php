<?php

namespace App\Filament\User\Resources\ProfileResource\Pages;

use App\Filament\User\Resources\ProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProfile extends ViewRecord
{
    protected static string $resource = ProfileResource::class;

    protected static ?string $breadcrumb = 'Görüntüle';

    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make(),
          
            
            
        ];
    }

    protected static ?string $title = 'Profilim';
 

    protected static ?string $breadcrumbParent = 'Kullanıcı';
}
