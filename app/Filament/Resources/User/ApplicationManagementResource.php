<?php

namespace App\Filament\Resources\User;

use App\Filament\Resources\User\ApplicationManagementResource\Pages;
use App\Filament\Resources\User\ApplicationManagementResource\RelationManagers;
use App\Filament\Resources\User\ApplicationManagementResource\RelationManagers\DocumentsRelationManager;
use App\Filament\Resources\User\ApplicationManagementResource\RelationManagers\InterviewsRelationManager;
use App\Models\Applications;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class ApplicationManagementResource extends Resource
{
    protected static ?string $model = Applications::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    
    protected static ?string $navigationLabel = 'Başvuru Yönetimi';
    
    protected static ?int $navigationSort = 1;
    
    protected static ?string $navigationGroup = 'Başvuru İşlemleri';

    protected static ?string $title = 'Başvuru Yönetimi';

    protected static ?string $breadcrumb = 'Başvuru İşlemleri';

    protected static ?string $breadcrumbParent = 'Başvuru Yönetimi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Başvuru Bilgileri')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Öğrenci')
                            ->relationship('user', 'name')
                            ->preload()
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('program_id')
                            ->label('Program')
                            ->relationship('program', 'name')
                            ->preload()
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Durum')
                            ->options([
                                'burs_havuzu' => 'Başvuru Havuzu',
                                'on_kabul' => 'Ön Kabul',
                                'red_edildi' => 'Reddedildi',
                                'evrak_bekleniyor' => 'Evrak Bekleniyor',
                                'evrak_incelemede' => 'Evrak İncelemede',
                                'mulakat_havuzu' => 'Mülakat Havuzu',
                                'mulakat_planlandi' => 'Mülakat Planlandı',
                                'mulakat_tamamlandi' => 'Mülakat Tamamlandı',
                                'kabul_edildi' => 'Kabul Edildi',
                                'kesin_kabul' => 'Kesin Kabul',
                                'onceki_burslu' => 'Önceki Burslu',
                            ])
                            ->required(),
                        Forms\Components\DatePicker::make('application_date')
                            ->label('Başvuru Tarihi')
                            ->default(now()),
                        Forms\Components\Toggle::make('are_documents_approved')
                            ->label('Evraklar Onaylandı mı?')
                            ->default(false),
                        Forms\Components\Toggle::make('is_interview_completed')
                            ->label('Mülakat Tamamlandı mı?')
                            ->default(false),
                    ])->columns(2),
                
                Forms\Components\Section::make('Öğrenci Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('university')
                            ->label('Üniversite')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('department')
                            ->label('Bölüm')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('current_year')
                            ->label('Mevcut Yıl')
                            ->numeric(),
                        Forms\Components\TextInput::make('gpa')
                            ->label('Not Ortalaması')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(4.0),
                    ])->columns(2),
                
                Forms\Components\Section::make('Mali Bilgiler')
                    ->schema([
                        Forms\Components\TextInput::make('income')
                            ->label('Gelir')
                            ->numeric()
                            ->prefix('₺'),
                        Forms\Components\Select::make('housing_status')
                            ->label('Barınma Durumu')
                            ->options([
                                'with_family' => 'Aile ile birlikte',
                                'dormitory' => 'Yurt',
                                'rental' => 'Kiralık',
                                'own_property' => 'Kendi mülkü',
                                'other' => 'Diğer',
                            ]),
                        Forms\Components\Textarea::make('financial_need_explanation')
                            ->label('Mali İhtiyaç Açıklaması')
                            ->columnSpanFull(),
                    ])->columns(2),
                
                Forms\Components\Section::make('Değerlendirme')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Notlar')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Reddetme Sebebi')
                            ->columnSpanFull()
                            ->visible(fn (Forms\Get $get) => $get('status') === 'red_edildi'),
                        Forms\Components\DatePicker::make('approval_date')
                            ->label('Onay Tarihi')
                            ->visible(fn (Forms\Get $get) => in_array($get('status'), ['kabul_edildi', 'kesin_kabul'])),
                        Forms\Components\Hidden::make('on_kabul_by')
                            ->default(fn () => Auth::id())
                            ->visible(fn (Forms\Get $get) => $get('status') === 'on_kabul'),
                        Forms\Components\Hidden::make('red_by')
                            ->default(fn () => Auth::id())
                            ->visible(fn (Forms\Get $get) => $get('status') === 'red_edildi'),
                        Forms\Components\Hidden::make('evrak_inceleme_by')
                            ->default(fn () => Auth::id())
                            ->visible(fn (Forms\Get $get) => $get('status') === 'evrak_incelemede'),
                        Forms\Components\Hidden::make('mulakat_havuzu_by')
                            ->default(fn () => Auth::id())
                            ->visible(fn (Forms\Get $get) => $get('status') === 'mulakat_havuzu'),
                        Forms\Components\Hidden::make('kabul_by')
                            ->default(fn () => Auth::id())
                            ->visible(fn (Forms\Get $get) => $get('status') === 'kabul_edildi'),
                        Forms\Components\Hidden::make('kesin_kabul_by')
                            ->default(fn () => Auth::id())
                            ->visible(fn (Forms\Get $get) => $get('status') === 'kesin_kabul'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Başvuru yönetiminde başvuru bulunamadı')
            ->emptyStateDescription('Yeni bir başvuru oluşturmak için "Yeni Başvuru Yönetimi" düğmesine tıklayın.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Yeni Başvuru Yönetimi')
                    ->icon('heroicon-o-user-group'),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Öğrenci')
                    ->searchable(),
                Tables\Columns\TextColumn::make('program.name')
                    ->label('Program')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'burs_havuzu' => 'Başvuru Havuzu',
                        'on_kabul' => 'Ön Kabul',
                        'red_edildi' => 'Reddedildi',
                        'evrak_bekleniyor' => 'Evrak Bekleniyor',
                        'evrak_incelemede' => 'Evrak İncelemede',
                        'mulakat_havuzu' => 'Mülakat Havuzu',
                        'mulakat_planlandi' => 'Mülakat Planlandı',
                        'mulakat_tamamlandi' => 'Mülakat Tamamlandı',
                        'kabul_edildi' => 'Kabul Edildi',
                        'kesin_kabul' => 'Kesin Kabul',
                        'onceki_burslu' => 'Önceki Burslu',
                        default => $state,
                    })
                    ->colors([
                        'secondary' => 'burs_havuzu',
                        'primary' => 'on_kabul',
                        'danger' => 'red_edildi',
                        'warning' => 'evrak_bekleniyor',
                        'info' => ['evrak_incelemede', 'mulakat_havuzu', 'mulakat_planlandi', 'mulakat_tamamlandi'],
                        'success' => ['kabul_edildi', 'kesin_kabul'],
                        'gray' => 'onceki_burslu',
                    ]),
                Tables\Columns\TextColumn::make('university')
                    ->label('Üniversite')
                    ->searchable(),
                Tables\Columns\TextColumn::make('department')
                    ->label('Bölüm')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('gpa')
                    ->label('Not Ortalaması')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('are_documents_approved')
                    ->label('Evraklar Onaylı')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_interview_completed')
                    ->label('Mülakat Tamamlı')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('application_date')
                    ->label('Başvuru Tarihi')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma Tarihi')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'burs_havuzu' => 'Başvuru Havuzu',
                        'on_kabul' => 'Ön Kabul',
                        'red_edildi' => 'Reddedildi',
                        'evrak_bekleniyor' => 'Evrak Bekleniyor',
                        'evrak_incelemede' => 'Evrak İncelemede',
                        'mulakat_havuzu' => 'Mülakat Havuzu',
                        'mulakat_planlandi' => 'Mülakat Planlandı',
                        'mulakat_tamamlandi' => 'Mülakat Tamamlandı',
                        'kabul_edildi' => 'Kabul Edildi',
                        'kesin_kabul' => 'Kesin Kabul',
                        'onceki_burslu' => 'Önceki Burslu',
                    ]),
                Tables\Filters\SelectFilter::make('program_id')
                    ->label('Program')
                    ->relationship('program', 'name'),
                Tables\Filters\Filter::make('high_gpa')
                    ->label('Yüksek Not Ortalaması')
                    ->query(fn (Builder $query): Builder => $query->where('gpa', '>=', 3.5))
                    ->toggle(),
                Tables\Filters\Filter::make('recent_applications')
                    ->label('Son Başvurular')
                    ->query(fn (Builder $query): Builder => $query->where('application_date', '>=', now()->subDays(30)))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Görüntüle'),
                Tables\Actions\EditAction::make()
                    ->label('Düzenle'),
                Tables\Actions\Action::make('pre_approve')
                    ->label('Ön Kabul Et')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(function (Applications $record) {
                        $record->status = 'on_kabul';
                        $record->on_kabul_by = Auth::id();
                        $record->on_kabul_at = now();
                        $record->save();
                    })
                    ->visible(fn (Applications $record): bool => $record->status === 'burs_havuzu'),
                Tables\Actions\Action::make('reject')
                    ->label('Reddet')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Reddetme Sebebi')
                            ->required(),
                    ])
                    ->action(function (Applications $record, array $data) {
                        $record->status = 'red_edildi';
                        $record->red_by = Auth::id();
                        $record->red_at = now();
                        $record->rejection_reason = $data['rejection_reason'];
                        $record->save();
                    })
                    ->visible(fn (Applications $record): bool => !in_array($record->status, ['red_edildi', 'kesin_kabul'])),
                Tables\Actions\Action::make('request_documents')
                    ->label('Evrak İste')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('primary')
                    ->action(function (Applications $record) {
                        $record->status = 'evrak_bekleniyor';
                        $record->save();
                        
                        // Bursiyer'e bildirim gönderme mantığı burada eklenebilir
                    })
                    ->visible(fn (Applications $record): bool => $record->status === 'on_kabul'),
                Tables\Actions\Action::make('move_to_interview')
                    ->label('Mülakata Gönder')
                    ->icon('heroicon-o-user-group')
                    ->color('info')
                    ->action(function (Applications $record) {
                        $record->status = 'mulakat_havuzu';
                        $record->mulakat_havuzu_by = Auth::id();
                        $record->mulakat_havuzu_at = now();
                        $record->save();
                    })
                    ->visible(fn (Applications $record): bool => $record->status === 'evrak_incelemede' && $record->are_documents_approved),
                Tables\Actions\Action::make('approve')
                    ->label('Kabul Et')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->action(function (Applications $record) {
                        $record->status = 'kabul_edildi';
                        $record->kabul_by = Auth::id();
                        $record->kabul_at = now();
                        $record->approval_date = now();
                        $record->save();
                    })
                    ->visible(fn (Applications $record): bool => $record->status === 'mulakat_tamamlandi' && $record->is_interview_completed),
                Tables\Actions\Action::make('final_approve')
                    ->label('Kesin Kabul')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->action(function (Applications $record) {
                        $record->status = 'kesin_kabul';
                        $record->kesin_kabul_by = Auth::id();
                        $record->kesin_kabul_at = now();
                        $record->save();
                    })
                    ->visible(fn (Applications $record): bool => $record->status === 'kabul_edildi'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_pre_approve')
                        ->label('Toplu Ön Kabul')
                        ->icon('heroicon-o-check')
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                if ($record->status === 'burs_havuzu') {
                                    $record->status = 'on_kabul';
                                    $record->on_kabul_by = Auth::id();
                                    $record->on_kabul_at = now();
                                    $record->save();
                                }
                            }
                        }),
                    Tables\Actions\BulkAction::make('bulk_request_documents')
                        ->label('Toplu Evrak İsteği')
                        ->icon('heroicon-o-document-arrow-down')
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                if ($record->status === 'on_kabul') {
                                    $record->status = 'evrak_bekleniyor';
                                    $record->save();
                                }
                            }
                        }),
                    Tables\Actions\BulkAction::make('bulk_move_to_interview')
                        ->label('Toplu Mülakata Gönder')
                        ->icon('heroicon-o-user-group')
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                if ($record->status === 'evrak_incelemede' && $record->are_documents_approved) {
                                    $record->status = 'mulakat_havuzu';
                                    $record->mulakat_havuzu_by = Auth::id();
                                    $record->mulakat_havuzu_at = now();
                                    $record->save();
                                }
                            }
                        }),
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Sil'),
                ]),
            ])
            ->defaultSort('application_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DocumentsRelationManager::make(),
            RelationManagers\InterviewsRelationManager::make(),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApplicationManagement::route('/'),
            'create' => Pages\CreateApplicationManagement::route('/create'),
            'view' => Pages\ViewApplicationManagement::route('/{record}'),
            'edit' => Pages\EditApplicationManagement::route('/{record}/edit'),
        ];
    }
}
