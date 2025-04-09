<?php

namespace App\Filament\Resources\ScholarshipProgramResource\Pages;

use App\Filament\Resources\ScholarshipProgramResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;
class ViewScholarshipProgram extends ViewRecord
{
    protected static string $resource = ScholarshipProgramResource::class;

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

    protected static ?string $title = 'Burs Programı Listesi';

    protected static ?string $breadcrumb = 'Burs Programları';

    protected static ?string $breadcrumbParent = 'Programlar';



    public function getTitle(): string
    {
        return 'Burs Programını Görüntüle';
    }


    

}
