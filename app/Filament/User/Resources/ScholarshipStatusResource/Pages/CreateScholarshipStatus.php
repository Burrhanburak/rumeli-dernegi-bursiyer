<?php

namespace App\Filament\User\Resources\ScholarshipStatusResource\Pages;

use App\Filament\User\Resources\ScholarshipStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateScholarshipStatus extends CreateRecord
{
    protected static string $resource = ScholarshipStatusResource::class;

    protected static ?string $title = 'Burs Durumumu Oluştur';

    protected static ?string $breadcrumb = 'Oluştur';

    protected static ?string $breadcrumbParent = 'Burs Durumumu';

    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     $data['user_id'] = Auth::id();
        
    //     if (!isset($data['status'])) {
    //         $data['status'] = 'aktif';
    //     }
        
    //     return $data;
    // }
}
