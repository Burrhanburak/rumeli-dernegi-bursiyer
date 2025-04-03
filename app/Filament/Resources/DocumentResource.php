<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentResource\Pages;
use App\Filament\Resources\DocumentResource\RelationManagers;
use App\Models\Documents;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentResource extends Resource
{
    protected static ?string $model = Documents::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationGroup = 'Belge Yönetimi';
    
    protected static ?int $navigationSort = 1;
    
    protected static ?string $navigationLabel = 'Belgeler';
    
    protected static ?string $title = 'Belgeler';

    protected static ?string $breadcrumb = 'Belgeler';

    protected static ?string $breadcrumbParent = 'Belgeler';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Belge Bilgileri')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('Kullanıcı')
                            ->required()
                            ->searchable(),
                        Forms\Components\Select::make('document_type_id')
                            ->relationship('documentType', 'name')
                            ->label('Belge Türü')
                            ->required()
                            ->searchable(),
                        Forms\Components\TextInput::make('name')
                            ->label('Belge Adı')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\FileUpload::make('file_path')
                            ->label('Dosya')
                            ->required()
                            ->disk('public')
                            ->directory('documents')
                            ->visibility('private')
                            ->maxSize(5120), // 5MB
                        Forms\Components\Textarea::make('description')
                            ->label('Açıklama')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('status')
                            ->label('Durum')
                            ->options([
                                'pending' => 'Beklemede',
                                'approved' => 'Onaylandı',
                                'rejected' => 'Reddedildi',
                            ])
                            ->default('pending')
                            ->required(),
                        Forms\Components\Textarea::make('admin_comment')
                        ->label('Yönetici Yorumu')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                         
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Belgeler bulunamadı')
            ->emptyStateDescription('Yeni bir belge oluşturmak için "Yeni Belgeler" düğmesine tıklayın.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Yeni Belgeler')
                    ->icon('heroicon-o-document-plus')
            ])
            ->columns([
                Tables\Columns\ImageColumn::make('file_path')
                ->label('Belge')
                ->height(20)
                ->disk('public')
                ->visibility('public'),
                Tables\Columns\TextColumn::make('id')
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
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Beklemede',
                        'approved' => 'Onaylandı',
                        'rejected' => 'Reddedildi',
                        default => 'Beklemede',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Yükleme Tarihi')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('verification_date')
                    ->label('Doğrulama Tarihi')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('verifier.name')
                    ->label('Doğrulayan')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Filters\SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'pending' => 'Beklemede',
                        'approved' => 'Onaylandı',
                        'rejected' => 'Reddedildi',
                    ]),
                Tables\Filters\SelectFilter::make('document_type_id')
                    ->relationship('documentType', 'name')
                    ->label('Belge Türü'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Görüntüle'),
                Tables\Actions\EditAction::make()
                    ->label('Düzenle'),
                Tables\Actions\Action::make('download')
                ->label('İndir')
                ->url(fn ($record) => $record->file_path ? \Storage::url($record->file_path) : null)
                ->icon('heroicon-o-arrow-down-tray')
                ->openUrlInNewTab()
                ->extraAttributes(['download' => true])
                ->color('success'),
                Tables\Actions\Action::make('approve')
                    ->label('Onayla')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'approved',
                            'reviewed_at' => now(),
                            'reviewed_by' => auth()->id(),
                        ]);
                        
                        // Belge onaylandıktan sonra başvurunun diğer belgelerini kontrol edelim
                        if ($record->application_id) {
                            $application = \App\Models\Applications::find($record->application_id);
                            if ($application) {
                                // Program için gerekli belgeleri bulalım
                                $requiredDocuments = \App\Models\ProgramDocumentRequirement::where('program_id', $application->program_id)
                                    ->where('is_required', true)
                                    ->pluck('document_type_id')
                                    ->toArray();
                                
                                // Kullanıcının onaylanan belgelerini bulalım
                                $approvedDocuments = \App\Models\Documents::where('application_id', $application->id)
                                    ->where('status', 'approved')
                                    ->pluck('document_type_id')
                                    ->toArray();
                                
                                // Tüm gerekli belgeler onaylandı mı kontrol edelim
                                $allApproved = true;
                                foreach ($requiredDocuments as $docTypeId) {
                                    if (!in_array($docTypeId, $approvedDocuments)) {
                                        $allApproved = false;
                                        break;
                                    }
                                }
                                
                                // Tüm belgeler onaylandıysa başvuru durumunu güncelle
                                if ($allApproved && count($requiredDocuments) > 0) {
                                    $application->are_documents_approved = true;
                                    $application->status = 'dogrulama_tamamlandi';
                                    $application->document_reviewed_by = auth()->id();
                                    $application->document_reviewed_at = now();
                                    $application->save();
                                    
                                    // Bildirim gösterelim
                                    \Filament\Notifications\Notification::make()
                                        ->title('Belgeler Doğrulandı')
                                        ->body('Tüm gerekli belgeler onaylandı ve başvuru durumu güncellendi.')
                                        ->success()
                                        ->send();
                                }
                            }
                        }
                    })
                    ->visible(fn ($record) => $record->status === 'pending'),
                Tables\Actions\Action::make('reject')
                    ->label('Reddet')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Ret Nedeni')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'reviewed_at' => now(),
                            'reviewed_by' => auth()->id(),
                            'reason' => $data['reason'],
                        ]);
                        
                        // Create a notification for the user about the document rejection
                        \App\Models\Notifications::create([
                            'notifiable_id' => $record->user_id,
                            'notifiable_type' => \App\Models\User::class,
                            'title' => 'Belgeniz Reddedildi',
                            'message' => 'Yüklediğiniz belge incelenmiş ve reddedilmiştir. Red nedeni: ' . $data['reason'],
                            'type' => 'document_rejected',
                            'document_id' => $record->id,
                            'is_read' => false,
                        ]);
                        
                        // Show notification to admin
                        \Filament\Notifications\Notification::make()
                            ->title('Belge Reddedildi')
                            ->body('Belge başarıyla reddedildi ve kullanıcıya bildirim gönderildi.')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Belge reddedilsin mi?')
                    ->modalDescription('Bu belgeyi reddetmek istediğinizden emin misiniz? Kullanıcıya ret nedeni ile birlikte bildirim gönderilecektir.')
                    ->modalSubmitActionLabel('Evet, Reddet')
                    ->visible(fn ($record) => $record->status === 'pending'),
                Tables\Actions\DeleteAction::make()
                    ->label('Sil')
                    ->requiresConfirmation()
                    ->modalHeading('Belge silinsin mi?')
                    ->modalDescription('Bu belgeyi silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')
                    ->modalSubmitActionLabel('Evet, Sil')
                    ->action(function ($record) {
                        // Delete the record
                        $record->delete();
                        
                        // Show notification to admin
                        \Filament\Notifications\Notification::make()
                            ->title('Belge Silindi')
                            ->body('Belge başarıyla silindi.')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Sil')
                        ->requiresConfirmation()
                        ->modalHeading('Belgeler silinsin mi?')
                        ->modalDescription('Seçili belgeleri silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')
                        ->modalSubmitActionLabel('Evet, Sil')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                // Delete the record
                                $record->delete();
                            }
                            
                            // Show notification to admin
                            \Filament\Notifications\Notification::make()
                                ->title('Belgeler Silindi')
                                ->body('Seçili belgeler başarıyla silindi.')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('approve_multiple')
                        ->label('Toplu Onayla')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update([
                                    'status' => 'approved',
                                    'reviewed_at' => now(),
                                    'reviewed_by' => auth()->id(),
                                ]);
                            }
                        }),
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
            'index' => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'view' => Pages\ViewDocument::route('/{record}'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
        ];
    }
}
