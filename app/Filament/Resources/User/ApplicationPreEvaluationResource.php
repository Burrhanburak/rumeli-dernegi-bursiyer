<?php

namespace App\Filament\Resources\User;

use App\Filament\Resources\User\ApplicationPreEvaluationResource\Pages;
use App\Models\Applications;
use App\Models\ScholarshipProgram;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class ApplicationPreEvaluationResource extends Resource
{
    protected static ?string $model = Applications::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    
    protected static ?string $navigationLabel = 'Ön Değerlendirme';
    
    protected static ?int $navigationSort = 1;
    
    protected static ?string $navigationGroup = 'Başvuru İşlemleri';

    protected static ?string $title = 'Başvuru Ön Değerlendirme';

    protected static ?string $breadcrumb = 'Ön Değerlendirme';

    protected static ?string $breadcrumbParent = 'Başvuru İşlemleri';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('status', ['beklemede', 'belgeler_yuklendi', 'dogrulama_bekliyor'])
            ->orderBy('updated_at', 'desc');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Başvuru Bilgileri')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Başvuran')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('program_id')
                            ->label('Program')
                            ->relationship('program', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('application_number')
                            ->label('Başvuru Numarası'),
                        Forms\Components\DatePicker::make('application_date')
                            ->label('Başvuru Tarihi'),
                        Forms\Components\Select::make('status')
                            ->label('Durum')
                            ->options([
                                'beklemede' => 'Beklemede',
                                'belgeler_yuklendi' => 'Belgeler Yüklendi',
                                'dogrulama_bekliyor' => 'Doğrulama Bekliyor',
                                'dogrulama_tamamlandi' => 'Doğrulama Tamamlandı',
                                'burs_havuzu' => 'Başvuru Havuzu',
                                'on_kabul' => 'Ön Kabul',
                                'red_edildi' => 'Reddedildi',
                            ]),
                    ])->columns(2),
                
                Forms\Components\Section::make('Değerlendirme')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Notlar')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Ret Sebebi')
                            ->maxLength(65535)
                            ->columnSpanFull()
                            ->visible(fn (Forms\Get $get) => $get('status') === 'red_edildi'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Bekleyen başvuru bulunamadı')
            ->emptyStateDescription('Tüm başvurular değerlendirilmiş.')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('application_number')
                    ->label('Başvuru No')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Başvuran')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('program.name')
                    ->label('Program')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('application_date')
                    ->label('Başvuru Tarihi')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'beklemede' => 'gray',
                        'belgeler_yuklendi' => 'info',
                        'dogrulama_bekliyor' => 'warning',
                        'dogrulama_tamamlandi' => 'success',
                        'burs_havuzu' => 'primary',
                        'on_kabul' => 'success',
                        'red_edildi' => 'danger',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'beklemede' => 'Beklemede',
                        'belgeler_yuklendi' => 'Belgeler Yüklendi',
                        'dogrulama_bekliyor' => 'Doğrulama Bekliyor',
                        'dogrulama_tamamlandi' => 'Doğrulama Tamamlandı',
                        'burs_havuzu' => 'Başvuru Havuzu',
                        'on_kabul' => 'Ön Kabul',
                        'red_edildi' => 'Reddedildi',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Son Güncelleme')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'beklemede' => 'Beklemede',
                        'belgeler_yuklendi' => 'Belgeler Yüklendi',
                        'dogrulama_bekliyor' => 'Doğrulama Bekliyor',
                    ]),
                Tables\Filters\SelectFilter::make('program')
                    ->label('Program')
                    ->relationship('program', 'name'),
                Tables\Filters\Filter::make('application_date')
                    ->label('Başvuru Tarihi')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Başlangıç'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Bitiş'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->where('application_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->where('application_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Görüntüle'),
                Tables\Actions\EditAction::make()
                    ->label('Düzenle'),
                Tables\Actions\Action::make('verify_documents')
                    ->label('Belgeleri Doğrula')
                    ->icon('heroicon-o-document-check')
                    ->color('success')
                    ->action(function (Applications $record) {
                        $record->status = 'dogrulama_tamamlandi';
                        $record->are_documents_approved = true;
                        $record->save();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Belgeler doğrulansın mı?')
                    ->modalDescription('Bu başvurunun belgelerini doğrulamak istediğinizden emin misiniz?')
                    ->modalSubmitActionLabel('Evet, Doğrula')
                    ->visible(fn (Applications $record): bool => $record->status === 'dogrulama_bekliyor'),
                Tables\Actions\Action::make('move_to_pool')
                    ->label('Havuza Taşı')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('primary')
                    ->action(function (Applications $record) {
                        $record->status = 'burs_havuzu';
                        $record->save();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Başvuru havuza taşınsın mı?')
                    ->modalDescription('Bu başvuruyu başvuru havuzuna taşımak istediğinizden emin misiniz?')
                    ->modalSubmitActionLabel('Evet, Havuza Taşı')
                    ->visible(fn (Applications $record): bool => $record->status === 'dogrulama_tamamlandi' && $record->are_documents_approved),
                Tables\Actions\Action::make('pre_approve')
                    ->label('Ön Kabul')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(function (Applications $record) {
                        $record->status = 'on_kabul';
                        $record->on_kabul_by = Auth::id();
                        $record->on_kabul_at = now();
                        $record->save();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Başvuru ön kabul edilsin mi?')
                    ->modalDescription('Bu başvuruyu ön kabul etmek istediğinizden emin misiniz?')
                    ->modalSubmitActionLabel('Evet, Ön Kabul Et')
                    ->visible(fn (Applications $record): bool => $record->status === 'dogrulama_tamamlandi' && $record->are_documents_approved),
                Tables\Actions\Action::make('reject')
                    ->label('Reddet')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Ret Sebebi')
                            ->required(),
                    ])
                    ->action(function (Applications $record, array $data) {
                        $record->status = 'red_edildi';
                        $record->red_by = Auth::id();
                        $record->red_at = now();
                        $record->rejection_reason = $data['rejection_reason'];
                        $record->save();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Başvuru reddedilsin mi?')
                    ->modalDescription('Bu başvuruyu reddetmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')
                    ->modalSubmitActionLabel('Evet, Reddet'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_verify_documents')
                        ->label('Belgeleri Doğrula')
                        ->icon('heroicon-o-document-check')
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                if ($record->status === 'dogrulama_bekliyor') {
                                    $record->status = 'dogrulama_tamamlandi';
                                    $record->are_documents_approved = true;
                                    $record->save();
                                }
                            }
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Belgeler doğrulansın mı?')
                        ->modalDescription('Seçili başvuruların belgelerini doğrulamak istediğinizden emin misiniz?')
                        ->modalSubmitActionLabel('Evet, Doğrula'),
                    Tables\Actions\BulkAction::make('bulk_move_to_pool')
                        ->label('Havuza Taşı')
                        ->icon('heroicon-o-arrow-right-circle')
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                if ($record->status === 'dogrulama_tamamlandi' && $record->are_documents_approved) {
                                    $record->status = 'burs_havuzu';
                                    $record->save();
                                }
                            }
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Başvurular havuza taşınsın mı?')
                        ->modalDescription('Seçili başvuruları başvuru havuzuna taşımak istediğinizden emin misiniz?')
                        ->modalSubmitActionLabel('Evet, Havuza Taşı'),
                    Tables\Actions\BulkAction::make('bulk_pre_approve')
                        ->label('Ön Kabul')
                        ->icon('heroicon-o-check')
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                if ($record->status === 'dogrulama_tamamlandi' && $record->are_documents_approved) {
                                    $record->status = 'on_kabul';
                                    $record->on_kabul_by = Auth::id();
                                    $record->on_kabul_at = now();
                                    $record->save();
                                }
                            }
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Başvurular ön kabul edilsin mi?')
                        ->modalDescription('Seçili başvuruları ön kabul etmek istediğinizden emin misiniz?')
                        ->modalSubmitActionLabel('Evet, Ön Kabul Et'),
                    Tables\Actions\BulkAction::make('bulk_reject')
                        ->label('Reddet')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->form([
                            Forms\Components\Textarea::make('rejection_reason')
                                ->label('Ret Sebebi')
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            foreach ($records as $record) {
                                $record->status = 'red_edildi';
                                $record->red_by = Auth::id();
                                $record->red_at = now();
                                $record->rejection_reason = $data['rejection_reason'];
                                $record->save();
                            }
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Başvurular reddedilsin mi?')
                        ->modalDescription('Seçili başvuruları reddetmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')
                        ->modalSubmitActionLabel('Evet, Reddet'),
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Sil'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApplicationPreEvaluations::route('/'),
            'create' => Pages\CreateApplicationPreEvaluation::route('/create'),
            'view' => Pages\ViewApplicationPreEvaluation::route('/{record}'),
            'edit' => Pages\EditApplicationPreEvaluation::route('/{record}/edit'),
        ];
    }
} 