<?php

namespace App\Filament\Resources\User\ScholarshipStudentsResource\Pages;

use App\Filament\Resources\User\ScholarshipStudentsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListScholarshipStudents extends ListRecords
{
    protected static string $resource = ScholarshipStudentsResource::class;

    protected static ?string $title = 'Burs Alan Öğrenciler';

    protected static ?string $breadcrumb = 'Burs Alan Öğrenciler';

    protected function getHeaderActions(): array
    {
        return [
            
        ];
    }
} 