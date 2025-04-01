<?php

namespace App\Filament\User\Resources\ApplicationsResource\Pages;

use App\Filament\User\Resources\ApplicationsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Filament\Tables\Actions\Action;

class ListApplications extends ListRecords
{
    protected static string $resource = ApplicationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Başvuru Oluştur')
            ->icon('heroicon-o-plus'),
           
        
        ];
    }
    protected static ?string $title = 'Başvurular';
    protected static ?string $breadcrumb = 'Başvurular';

    public function getTabs(): array
    {
        return [
            'Tum basvurular' => Tab::make()
                ->label('Tüm Başvurular')
                ->icon('heroicon-o-document-text')
                ->badge(ApplicationsResource::getModel()::count())
                ->badgeColor('success')
                ,
            'pending' => Tab::make()
                ->label('Bekleyen Başvurular')
           
                ->icon('heroicon-o-clock')
      
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'awaiting_evaluation'))

               ,
            'approved' => Tab::make()
                ->label('Onaylı Başvurular')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'accepted')),
               
            'rejected' => Tab::make()
                ->label('Reddedilen Başvurular')
               
                ->icon('heroicon-o-x-circle')
                
            ->modifyQueryUsing(fn ($query) => $query->where('status', 'rejected')),
            'completed' => Tab::make()
                ->label('Tamamlanan Başvurular')
                ->icon('heroicon-o-check')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'final_acceptance')),
              
            
        ];
    }
}
