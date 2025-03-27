<?php

namespace App\Filament\Resources\DocumentResource\Pages;

use App\Filament\Resources\DocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Support\Facades\Storage;

class ViewDocument extends ViewRecord
{
    protected static string $resource = DocumentResource::class;

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
                Actions\Action::make('download')
                ->label('İndir')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn () => secure_asset('storage/' . $this->record->file_path))
                ->openUrlInNewTab(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Ensure URLs are using HTTPS
        if (isset($data['file_path']) && $data['file_path']) {
            $data['file_path_url'] = secure_asset('storage/' . $data['file_path']);
        }
        
        return $data;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Belge Bilgileri')
                    ->schema([
                        Infolists\Components\TextEntry::make('documentType.name')
                            ->label('Belge Türü'),
                        Infolists\Components\TextEntry::make('name')
                            ->label('Belge Adı'),
                        Infolists\Components\TextEntry::make('description')
                            ->label('Açıklama'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Yükleme Tarihi')
                            ->date(),
                        Infolists\Components\TextEntry::make('is_verified')
                            ->label('Doğrulandı mı')
                            ->badge()
                            ->formatStateUsing(fn (bool $state): string => $state ? 'Evet' : 'Hayır')
                            ->color(fn (bool $state): string => $state ? 'success' : 'warning'),
                    ])->columns(2),
                Infolists\Components\Section::make('Belge Görüntüle')
                    ->schema([
                        Infolists\Components\ImageEntry::make('file_path')
                            ->label('Belge')
                            ->height(400)
                            ->disk('public')
                            ->visibility('public'),
                    ]),
            ]);
    }
}
