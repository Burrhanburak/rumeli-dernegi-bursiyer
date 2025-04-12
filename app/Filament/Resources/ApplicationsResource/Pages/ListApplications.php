<?php

namespace App\Filament\Resources\ApplicationsResource\Pages;

use App\Filament\Resources\ApplicationsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use App\Filament\Exports\ApplicationsExporter;

class ListApplications extends ListRecords
{
    protected static string $resource = ApplicationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Yeni Başvuru'),

      
              
        ];
    }

    protected static ?string $title = 'Başvurular';

    protected static ?string $breadcrumb = 'Başvurular listesi';

    protected static ?string $breadcrumbParent = 'Başvurular';

    protected static ?string $createButtonLabel = 'Yeni Başvuru';

    protected static ?string $createButtonIcon = 'heroicon-o-plus';

    protected static ?string $createButtonColor = 'success';

    public function getTabs(): array
    {
        return [
            'Tüm Başvurular' => Tab::make()
                ->label('Tüm Başvurular')
                ->icon('heroicon-o-document-text')
                ->badge(ApplicationsResource::getModel()::count())
                ->badgeColor('gray')
                ,
            'Bekleyen Başvurular' => Tab::make()
                ->label('Bekleyen Başvurular')
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'awaiting_evaluation')
                    ->orWhere('status', 'bekliyor')
                    ->orWhere('status', 'beklemede'))
                ->badge(ApplicationsResource::getModel()::where('status', 'awaiting_evaluation')
                    ->orWhere('status', 'bekliyor')
                    ->orWhere('status', 'beklemede')->count())
                ->badgeColor('gray')
                ,
            'Onaylanan Başvurular' => Tab::make()
                ->label('Onaylanan Başvurular')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'accepted')
                    ->orWhere('status', 'kabul_edildi')
                    ->orWhere('status', 'onaylandi')
                    ->orWhere('status', 'dogrulama_tamamlandi'))
                ->badge(ApplicationsResource::getModel()::where('status', 'accepted')
                    ->orWhere('status', 'kabul_edildi')
                    ->orWhere('status', 'onaylandi')
                    ->orWhere('status', 'dogrulama_tamamlandi')->count())
                ->badgeColor('gray')
                ,

            'Reddedilen Başvurular' => Tab::make()
                ->label('Reddedilen Başvurular')
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'rejected')
                    ->orWhere('status', 'reddedildi')
                    ->orWhere('status', 'red')
                    ->orWhere('status', 'red_edildi'))
                ->badge(ApplicationsResource::getModel()::where('status', 'rejected')
                    ->orWhere('status', 'reddedildi')
                    ->orWhere('status', 'red')
                    ->orWhere('status', 'red_edildi')->count())
                ->badgeColor('gray')
                ,
          
    
        ];
    }
}
