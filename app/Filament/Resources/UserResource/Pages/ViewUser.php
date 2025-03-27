<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;
    protected static ?string $title = 'Kullanıcı Görüntüle';
    protected static ?string $breadcrumb = 'Kullanıcılar';
    protected static ?string $breadcrumbParent = 'Kullanıcılar';

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
    
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Grid::make(3)
                    ->schema([
                        Infolists\Components\Section::make()
                            ->schema([
                                Infolists\Components\ImageEntry::make('image')
                                    ->label('Profil Fotoğrafı')
                                    ->circular()
                                    ->defaultImageUrl(function ($record) {
                                        $name = trim(($record->name ?? '') . ' ' . ($record->surname ?? ''));
                                        if (empty($name)) $name = 'Kullanıcı';
                                        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&size=200&background=808080&color=ffffff&bold=true&length=2';
                                    })
                                    ->size(100)
                                    ->extraAttributes(['class' => 'mx-auto']),
                                
                                Infolists\Components\Grid::make(2)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('name')
                                            ->label('Ad')
                                            ->weight('bold')
                                            ->color('gray'),
                                        
                                        Infolists\Components\TextEntry::make('surname')
                                            ->label('Soyad')
                                            ->weight('bold')
                                            ->color('gray'),
                                    ]),
                                
                                    Infolists\Components\TextEntry::make('is_admin')
                                    ->label('Yetki')
                                    ->badge()
                                    ->formatStateUsing(fn (bool $state): string => $state ? 'Yönetici' : 'Kullanıcı')
                                    ->color(fn (bool $state): string => $state ? 'success' : 'info')
                                    
                            ])
                            ->columnSpan(1),
                        
                        Infolists\Components\Section::make('İletişim Bilgileri')
                            ->schema([
                                Infolists\Components\TextEntry::make('email')
                                    ->label('E-posta')
                                    ->icon('heroicon-o-envelope')
                                    ->iconColor('primary')
                                    ->copyable()
                                    ->url(fn (string $state): string => "mailto:{$state}"),
                                
                                Infolists\Components\TextEntry::make('email_verified_at')
                                    ->label('E-posta Doğrulama Tarihi')
                                    ->icon('heroicon-o-check-badge')
                                    ->iconColor('success')
                                    ->date('d MMMM Y - H:i')
                                    ->formatStateUsing(fn ($state) => $state ? date('d MMMM Y - H:i', strtotime($state)) : 'Doğrulanmamış')
                                    ->color(fn ($state) => $state ? 'success' : 'danger'),
                                
                                Infolists\Components\TextEntry::make('phone')
                                    ->label('Telefon')
                                    ->icon('heroicon-o-phone')
                                    ->iconColor('primary')
                                    ->url(fn ($state): ?string => $state ? "tel:{$state}" : null)
                                    ->placeholder('Telefon numarası eklenmemiş')
                                    ->copyable(),
                                
                                Infolists\Components\TextEntry::make('address')
                                    ->label('Adres')
                                    ->icon('heroicon-o-map-pin')
                                    ->iconColor('primary')
                                    ->placeholder('Adres eklenmemiş'),
                            ])
                            ->columnSpan(2),
                        
                        Infolists\Components\Section::make('Kişisel Bilgiler')
                            ->schema([
                                Infolists\Components\TextEntry::make('birth_date')
                                    ->label('Doğum Tarihi')
                                    ->icon('heroicon-o-calendar')
                                    ->date('d MMMM Y')
                                    ->placeholder('Doğum tarihi eklenmemiş'),
                                
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Üyelik Tarihi')
                                    ->icon('heroicon-o-clock')
                                    ->date('d MMMM Y'),
                                
                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label('Son Güncelleme')
                                    ->icon('heroicon-o-arrow-path')
                                    ->since(),
                            ])
                            ->columnSpan(3),
                    ]),
            ]);
    }
}
