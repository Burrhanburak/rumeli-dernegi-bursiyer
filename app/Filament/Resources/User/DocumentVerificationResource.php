<?php

namespace App\Filament\Resources\User;

use App\Filament\Resources\User\DocumentVerificationResource\Pages;
use App\Filament\Resources\User\DocumentVerificationResource\RelationManagers;
use App\Models\Documents;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class DocumentVerificationResource extends Resource
{
    protected static ?string $model = Documents::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    
    protected static ?string $navigationLabel = 'Evrak Doğrulama';
    
    protected static ?int $navigationSort = 2;
    
    protected static ?string $navigationGroup = 'Başvuru İşlemleri';

    protected static ?string $title = 'Evrak Doğrulama';

    protected static ?string $breadcrumb = 'Evrak Doğrulama';

    protected static ?string $breadcrumbParent = 'Evrak Doğrulama';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('application', function (Builder $query) {
                $query->whereIn('status', ['evrak_bekleniyor', 'evrak_incelemede']);
            })
            ->where(function ($query) {
                $query->where('status', 'pending')
                      ->orWhereNull('status');
            });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Belge Bilgileri')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Kullanıcı')
                            ->relationship('user', 'name')
                            ->disabled()
                            ->dehydrated(false)
                            ->searchable(),
                        Forms\Components\Select::make('document_type_id')
                            ->label('Belge Türü')
                            ->relationship('documentType', 'name')
                            ->disabled()
                            ->dehydrated(false)
                            ->searchable(),
                        Forms\Components\TextInput::make('name')
                            ->label('Belge Adı')
                            ->disabled()
                            ->dehydrated(false)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('created_at')
                            ->label('Yükleme Tarihi')
                            ->disabled(),
                        Forms\Components\TextInput::make('description')
                            ->label('Açıklama')
                            ->disabled()
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('file_path')
                            ->label('Dosya')
                            ->disabled()
                            ->image()
                            ->disk('public')
                            ->directory('documents'),
                        Forms\Components\Toggle::make('is_verified')
                            ->label('Doğrulandı mı')
                            ->default(false)
                            ->required(),
                        Forms\Components\DatePicker::make('verification_date')
                            ->label('Doğrulama Tarihi')
                            ->default(now()),
                        Forms\Components\TextInput::make('rejection_reason')
                            ->label('Ret Nedeni')
                            ->maxLength(255)
                            ->hidden(fn (Forms\Get $get): bool => $get('is_verified') === true),
                        Forms\Components\Textarea::make('admin_comment')
                            ->label('Admin Yorumu')
                            ->maxLength(65535),
                    ])->columns(2),
                
                Forms\Components\Section::make('Doğrulama')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Durum')
                            ->options([
                                'pending' => 'Beklemede',
                                'verified' => 'Doğrulanmış',
                                'rejected' => 'Reddedilmiş',
                            ])
                            ->default('pending')
                            ->required(),
                        Forms\Components\Textarea::make('verification_notes')
                            ->label('Doğrulama Notları')
                            ->maxLength(65535),
                        Forms\Components\Hidden::make('verified_by')
                            ->default(fn () => Auth::id())
                            ->visible(fn (Forms\Get $get) => $get('status') === 'verified'),
                        Forms\Components\Hidden::make('verification_date')
                            ->default(now())
                            ->visible(fn (Forms\Get $get) => $get('status') === 'verified'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->emptyStateHeading('Bekleyen Belge Doğrulaması Yok')
            ->emptyStateDescription('Tüm belgeler doğrulanmış veya henüz yüklenmemiş.')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Kullanıcı')
                    ->searchable(),
                Tables\Columns\TextColumn::make('documentType.name')
                    ->label('Belge Türü')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Belge Adı')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Doğrulandı')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Yükleme Tarihi')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('verification_date')
                    ->label('Doğrulama Tarihi')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('application.status')
                    ->label('Başvuru Durumu')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'burs_havuzu' => 'Başvuru Alındı',
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
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_verified')
                    ->label('Doğrulama Durumu')
                    ->options([
                        '0' => 'Doğrulanmadı',
                        '1' => 'Doğrulandı',
                    ]),
                Tables\Filters\Filter::make('recent_uploads')
                    ->label('Son 7 Gün Yüklenen')
                    ->query(fn (Builder $query): Builder => $query->where('created_at', '>=', now()->subDays(7)))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Görüntüle'),
                Tables\Actions\EditAction::make()
                    ->label('Doğrula / Reddet'),
                Tables\Actions\Action::make('download')
                    ->label('İndir')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (Documents $record): string => route('filament.admin.resources.documents.download', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_approve')
                        ->label('Toplu Onayla')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                $record->status = 'verified';
                                $record->verified_by = Auth::id();
                                $record->verification_date = now();
                                $record->save();
                            }
                        }),
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Sil'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
    
    protected static function updateApplicationIfAllDocumentsVerified($application)
    {
        if (!$application) return;
        
        // Tüm belgeler onaylandı mı kontrol et
        $allVerified = true;
        $documents = $application->documents;
        
        if ($documents->count() === 0) {
            return;
        }
        
        foreach ($documents as $document) {
            if ($document->status !== 'verified') {
                $allVerified = false;
                break;
            }
        }
        
        if ($allVerified) {
            $application->are_documents_approved = true;
            $application->status = 'evrak_incelemede';
            $application->evrak_inceleme_by = Auth::id();
            $application->evrak_inceleme_at = now();
            $application->save();
        }
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
            'index' => Pages\ListDocumentVerifications::route('/'),
            'create' => Pages\CreateDocumentVerification::route('/create'),
            'view' => Pages\ViewDocumentVerification::route('/{record}'),
            'edit' => Pages\EditDocumentVerification::route('/{record}/edit'),
        ];
    }
}
