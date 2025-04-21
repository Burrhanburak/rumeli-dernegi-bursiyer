<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Yeni Kullanıcı')
                ->icon('heroicon-o-user-plus'),

                ExportAction::make()
                ->label('İndir')
                ->exports([
                    ExcelExport::make()
                        ->fromTable()
                        ->withFilename(fn ($resource) => $resource::getModelLabel() . '-' . date('Y-m-d'))
                        ->withWriterType(\Maatwebsite\Excel\Excel::CSV)
                        ->withColumns([
                            Column::make('id')
                                ->heading('ID'),
                            Column::make('name')
                                ->heading('Ad'),
                            Column::make('surname')
                                ->heading('Soyad'),
                            Column::make('email')
                                ->heading('E-posta'),
                            Column::make('is_admin')
                                ->heading('Yetki'),
                            Column::make('national_id')
                                ->heading('T.C. Kimlik No'),
                            Column::make('birth_date')
                                ->heading('Doğum Tarihi'),
                            Column::make('phone')
                                ->heading('Telefon Numarası'),
                            Column::make('address')
                                ->heading('Adres'),
                            Column::make('city')
                                ->heading('Şehir'),
                            Column::make('postal_code')
                                ->heading('Posta Kodu'),
                            Column::make('email_verified_at')
                                ->heading('E-posta Doğrulanma Tarihi'),
                            Column::make('created_at')
                                ->heading('Oluşturulma Tarihi'),
                            Column::make('updated_at')
                                ->heading('Güncellenme Tarihi'),
                        ])
                ]), 
        ];
    }

    protected static ?string $title = 'Sistemdeki Kullanıcılar';

    protected static ?string $breadcrumb = 'Kullanıcılar';

    protected static ?string $breadcrumbParent = 'Kullanıcılar';
}
