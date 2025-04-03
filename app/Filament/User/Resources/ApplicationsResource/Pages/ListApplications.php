<?php

namespace App\Filament\User\Resources\ApplicationsResource\Pages;

use App\Filament\User\Resources\ApplicationsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;

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
        $userId = Auth::id();
        
        return [
            'Tum basvurular' => Tab::make()
                ->label('Tüm Başvurular')
                ->icon('heroicon-o-document-text')
                ->badge(ApplicationsResource::getModel()::where('user_id', $userId)->count())
                ->badgeColor('gray')
                ->modifyQueryUsing(fn ($query) => $query->where('user_id', $userId))
                ,
            'pending' => Tab::make()
                ->label('Bekleyen Başvurular')
                ->icon('heroicon-o-clock')
                ->badge(ApplicationsResource::getModel()::where('user_id', $userId)->where(function($query) {
                    $query->where('status', 'awaiting_evaluation')
                          ->orWhere('status', 'beklemede')
                          ->orWhere('status', 'bekleniyor')
                          ->orWhere('status', 'incelemede');
                })->count())
                ->badgeColor('gray')
                ->modifyQueryUsing(fn ($query) => $query->where('user_id', $userId)->where(function($query) {
                    $query->where('status', 'awaiting_evaluation')
                          ->orWhere('status', 'beklemede')
                          ->orWhere('status', 'bekleniyor')
                          ->orWhere('status', 'incelemede');
                })),
            'accepted' => Tab::make()
                ->label('Onaylı Başvurular')
                ->icon('heroicon-o-check-circle')
                ->badge(ApplicationsResource::getModel()::where('user_id', $userId)->where(function($query) {
                    $query->where('status', 'accepted')
                          ->orWhere('status', 'kabul_edildi');
                })->count())
                ->badgeColor('gray')
                ->modifyQueryUsing(fn ($query) => $query->where('user_id', $userId)->where(function($query) {
                    $query->where('status', 'accepted')
                          ->orWhere('status', 'kabul_edildi');
                })),
            'rejected' => Tab::make()
                ->label('Reddedilen Başvurular')
                ->icon('heroicon-o-x-circle')
                ->badge(ApplicationsResource::getModel()::where('user_id', $userId)->where(function($query) {
                    $query->where('status', 'rejected')
                          ->orWhere('status', 'reddedildi')
                          ->orWhere('status', 'red')
                          ->orWhereNotNull('rejected_at')
                          ->orWhereNotNull('rejected_by');
                })->count())
                ->badgeColor('gray')
                ->modifyQueryUsing(fn ($query) => $query->where('user_id', $userId)->where(function($query) {
                    $query->where('status', 'rejected')
                          ->orWhere('status', 'reddedildi')
                          ->orWhere('status', 'red')
                          ->orWhereNotNull('rejected_at')
                          ->orWhereNotNull('rejected_by');
                })),
       
            
        ];
    }
}

