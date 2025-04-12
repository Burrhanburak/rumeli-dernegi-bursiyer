<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScholarshipProgramResource\Pages;
use App\Filament\Resources\ScholarshipProgramResource\RelationManagers;
use App\Models\ScholarshipProgram;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;

class ScholarshipProgramResource extends Resource
{
    protected static ?string $model = ScholarshipProgram::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    
    protected static ?string $navigationGroup = 'Burs Yönetimi';
    
    protected static ?int $navigationSort = 2;
    
    protected static ?string $navigationLabel = 'Burs Programları';

    protected static ?string $title = 'Burs Programları';

    protected static ?string $breadcrumb = 'Burs Yönetimi';

    protected static ?string $breadcrumbParent = 'Burs Yönetimi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Program Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Program Adı')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\RichEditor::make('description')
                            ->label('Açıklama')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('default_amount')
                            ->label('Burs Tutarı')
                            ->numeric()
                            ->prefix('₺')
                            ->required(),
                        Forms\Components\TextInput::make('requirements')
                            ->label('Gereksinimler')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notlar')
                            ->columnSpanFull(),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Tarihler ve Durum')
                    ->schema([
                        Forms\Components\DatePicker::make('application_start_date')
                            ->label('Başvuru Başlangıç Tarihi')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->required(),
                        Forms\Components\DatePicker::make('application_end_date')
                            ->label('Başvuru Bitiş Tarihi')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->required()
                            ->after('application_start_date'),
                        Forms\Components\DatePicker::make('program_start_date')
                            ->label('Program Başlangıç Tarihi')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->required(),
                        Forms\Components\DatePicker::make('program_end_date')
                            ->label('Program Bitiş Tarihi')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->after('program_start_date'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif mi')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Program Durumu')
                            ->options([
                                'aktif' => 'Aktif',
                                'askida' => 'Askıda',
                                'sonlandirildi' => 'Sonlandırıldı',
                            ])
                            ->default('aktif')
                            ->required(),
                        Forms\Components\Textarea::make('status_reason')
                            ->label('Durum Değişiklik Sebebi')
                            ->nullable()
                            ->columnSpanFull()
                            ->visible(fn (callable $get) => $get('status') !== 'aktif'),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Kontenjan Bilgisi')
                    ->schema([
                        Forms\Components\TextInput::make('max_recipients')
                            ->label('Maksimum Alıcı Sayısı')
                            ->numeric()
                            ->nullable(),
                     
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Burs Programları bulunamadı')
            ->emptyStateDescription('Yeni bir burs programı oluşturmak için "Yeni Program" düğmesine tıklayın.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Burs Programı')
                    ->icon('heroicon-o-academic-cap')
                    ->successNotification(
                        Notification::make()
                            ->title('Burs Programı Oluşturuldu')
                            ->body('Burs programı başarıyla oluşturuldu.')
                            ->success()
                    ),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Program Adı')
                    ->searchable(),
                Tables\Columns\TextColumn::make('default_amount')
                    ->label('Tutar')
                    ->money('try')
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_recipients')
                    ->label('Kontenjan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Durum')
                    ->boolean(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Program Durumu')
                    ->badge()
                    ->tooltip(fn ($state): string => "Raw value: " . ($state ?? 'null'))
                    ->color(fn ($state): string => match ($state) {
                        'active', 'aktif', '1', 1, true, null, '' => 'success',
                        'suspended', 'askıya_alındı', 'askida' => 'warning',
                        'terminated', 'sonlandırıldı', 'sonlandirildi' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state): string => match ($state) {
                        'active', 'aktif', '1', 1, true, null, '' => 'Aktif',
                        'suspended', 'askıya_alındı', 'askida' => 'Askıda',
                        'terminated', 'sonlandırıldı', 'sonlandirildi' => 'Sonlandırıldı',
                        default => 'Belirsiz',
                    }),
                Tables\Columns\TextColumn::make('application_start_date')
                    ->label('Başvuru Başlangıcı')
                    ->date()
                    
                    ->sortable(),
                Tables\Columns\TextColumn::make('application_end_date')
                    ->label('Başvuru Bitişi')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('program_start_date')
                    ->label('Program Başlangıcı')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('program_end_date')
                    ->label('Program Bitişi')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma Tarihi')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Güncellenme Tarihi')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('is_active')
                    ->label('Aktif Programlar')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true))
                    ->toggle(),
                Tables\Filters\Filter::make('current')
                    ->label('Güncel Programlar')
                    ->query(fn (Builder $query): Builder => $query->where('program_start_date', '<=', now())->where('program_end_date', '>=', now()))
                    ->toggle(),
                Tables\Filters\Filter::make('accepting_applications')
                    ->label('Başvuruya Açık')
                    ->query(fn (Builder $query): Builder => $query->where('application_end_date', '>=', now()))
                    ->toggle(),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Program Durumu')
                    ->options([
                        'aktif' => 'Aktif',
                        'askida' => 'Askıda',
                        'sonlandirildi' => 'Sonlandırıldı',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Görüntüle'),
                Tables\Actions\EditAction::make()
                    ->label('Düzenle')
                    ->successNotification(
                        Notification::make()
                            ->title('Burs Programı Güncellendi')
                            ->body('Burs programı başarıyla güncellendi.')
                            ->success()
                    ),
                Tables\Actions\Action::make('suspend')
                    ->label('Askıya Al')
                    ->icon('heroicon-o-pause')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Programı Askıya Al')
                    ->modalDescription('Bu burs programını askıya almak istediğinize emin misiniz?')
                    ->modalSubmitActionLabel('Evet, Askıya Al')
                    ->modalCancelActionLabel('İptal')
                    ->visible(function (ScholarshipProgram $record) {
                        // Don't show if already suspended or terminated
                        return !in_array($record->status, ['askida', 'sonlandirildi']);
                    })
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Askıya Alma Sebebi')
                            ->required(),
                    ])
                    ->action(function (ScholarshipProgram $record, array $data) {
                        $record->status = 'askida';
                        $record->status_reason = $data['reason'];
                        $record->last_updated_by = auth()->id();
                        $record->save();
                        
                        Notification::make()
                            ->title('Program Askıya Alındı')
                            ->body('Burs programı başarıyla askıya alındı.')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('terminate')
                    ->label('Sonlandır')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Programı Sonlandır')
                    ->modalDescription('Bu burs programını sonlandırmak istediğinize emin misiniz?')
                    ->modalSubmitActionLabel('Evet, Sonlandır')
                    ->modalCancelActionLabel('İptal')
                    ->visible(function (ScholarshipProgram $record) {
                        // Don't show if already suspended or terminated
                        return !in_array($record->status, ['askida', 'sonlandirildi']);
                    })
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Sonlandırma Sebebi')
                            ->required(),
                    ])
                    ->action(function (ScholarshipProgram $record, array $data) {
                        $record->status = 'sonlandirildi';
                        $record->status_reason = $data['reason'];
                        $record->last_updated_by = auth()->id();
                        $record->save();
                        
                        Notification::make()
                            ->title('Program Sonlandırıldı')
                            ->body('Burs programı başarıyla sonlandırıldı.')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('activate')
                    ->label('Aktifleştir')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Programı Aktifleştir')
                    ->modalDescription('Bu burs programını aktifleştirmek istediğinize emin misiniz?')
                    ->modalSubmitActionLabel('Evet, Aktifleştir')
                    ->modalCancelActionLabel('İptal')
                    ->visible(function (ScholarshipProgram $record) {
                        // Only show if suspended or terminated
                        return in_array($record->status, ['askida', 'sonlandirildi']);
                    })
                    ->action(function (ScholarshipProgram $record) {
                        $record->status = 'aktif';
                        $record->status_reason = null;
                        $record->last_updated_by = auth()->id();
                        $record->save();
                        
                        Notification::make()
                            ->title('Program Aktifleştirildi')
                            ->body('Burs programı başarıyla aktifleştirildi.')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('manageDocuments')
                    ->label('Evrak Türleri')
                    ->color('warning')
                    // ->modalHeading('Evrak Türleri')
                    // ->modalDescription('Bu burs programı için gerekli olan evrak türlerini yönetin.')
                    // ->modalCancelActionLabel('İptal')
                    // ->modalSubmitActionLabel('Kaydet')
                    ->icon('heroicon-o-document')
                    ->url(fn (ScholarshipProgram $record): string => route('filament.admin.resources.scholarship-programs.edit', ['record' => $record, 'activeRelationManager' => 2])),
                Tables\Actions\DeleteAction::make()
                    ->label('Sil')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Burs Programını Sil')
                    ->modalDescription('Bu burs programını silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')
                    ->modalSubmitActionLabel('Evet, Sil')
                    ->modalCancelActionLabel('İptal')
                    ->successNotification(
                        Notification::make()
                            ->title('Burs Programı Silindi')
                            ->body('Burs programı başarıyla silindi.')
                            ->success()
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                    ->label('Tümünü Sil')
                    ->requiresConfirmation()
                    ->modalHeading('Belgeler silinsin mi?')
                    ->modalDescription('Seçili belgeleri silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')
                    ->modalSubmitActionLabel('Evet, Sil')
                    ->action(function ($records) {
                        foreach ($records as $record) {
                            // Delete the record
                            $record->delete();
                        }
                    }),
                ])
                ->label('Program İşlemleri'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ApplicationsRelationManager::make(),
            RelationManagers\ScholarshipsRelationManager::make(),
            RelationManagers\DocumentRequirementsRelationManager::make(),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListScholarshipPrograms::route('/'),
            'create' => Pages\CreateScholarshipProgram::route('/create'),
            'view' => Pages\ViewScholarshipProgram::route('/{record}'),
            'edit' => Pages\EditScholarshipProgram::route('/{record}/edit'),
        ];
    }
}