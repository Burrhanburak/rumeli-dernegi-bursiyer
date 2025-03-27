<?php

namespace App\Filament\Resources\DocumentTypeResource\Pages;

use App\Filament\Resources\DocumentTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDocumentType extends EditRecord
{
    protected static string $resource = DocumentTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label('Görüntüle')
                ->icon('heroicon-o-eye')
                ->color('info'),
            Actions\DeleteAction::make()
                ->label('Sil')
                ->icon('heroicon-o-trash')
                ->color('danger'),
        ];
        
    }
    
    protected static ?string $title = 'Belge Türü Düzenle';

    protected static ?string $breadcrumb = 'Belge Türü Düzenle';

    protected static ?string $breadcrumbParent = 'Belge Türleri';
    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('save')
                ->label('Kaydet')
                ->submit('save')
                ->keyBindings(['mod+s']),
            Actions\Action::make('cancel')
                ->label('İptal')
                ->url($this->previousUrl ?? static::getResource()::getUrl())
                ->color('secondary'),
        ];
    }

}
