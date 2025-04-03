<?php

namespace App\Filament\Resources\ApplicationsResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Documents;
use App\Models\DocumentType;
use App\Models\Applications;
use App\Models\User;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $label = 'Belgeler';
    protected static ?string $pluralLabel = 'Belgeler';
    protected static ?string $title = 'Kullanıcı Belgeleri';
    protected static ?string $navigationLabel = 'Belgeler';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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
                    ->maxSize(5120), // 5MB
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
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
        ->recordTitleAttribute('document_type_id')
            ->columns([
                Tables\Columns\TextColumn::make('documentType.name')
                    ->label('Belge Türü')
                    ->searchable(),
                    Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Beklemede',
                        'approved' => 'Onaylandı',
                        'rejected' => 'Reddedildi',
                        default => $state,
                    })
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ]),
                    Tables\Columns\TextColumn::make('admin_comment')
                    ->label('Yönetici Yorumu')
                    ->limit(30),
                Tables\Columns\TextColumn::make('name')
                    ->label('Belge Adı')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Yükleme Tarihi')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
              
                Tables\Filters\SelectFilter::make('document_type_id')
                    ->relationship('documentType', 'name')
                    ->label('Belge Türü'),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'pending' => 'Beklemede',
                        'approved' => 'Onaylandı',
                        'rejected' => 'Reddedildi',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Belge Ekle'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Görüntüle'),
                Tables\Actions\EditAction::make()
                    ->label('Düzenle'),
                Tables\Actions\DeleteAction::make()
                    ->label('Sil')
                    ->requiresConfirmation()
                    ->modalHeading('Belge silinsin mi?')
                    ->modalDescription('Bu belgeyi silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')
                    ->modalSubmitActionLabel('Evet, Sil')
                    ->action(function ($record) {
                        // Create a notification for the user about the document deletion
                        \App\Models\Notifications::create([
                            'notifiable_id' => $record->user_id,
                            'notifiable_type' => \App\Models\User::class,
                            'title' => 'Belgeniz Silindi',
                            'message' => 'Yüklediğiniz belge yönetici tarafından silindi.',
                            'type' => 'document_rejected',
                            'is_read' => false,
                        ]);
                        
                        // Delete the record
                        $record->delete();
                        
                        // Show notification to admin
                        \Filament\Notifications\Notification::make()
                            ->title('Belge Silindi')
                            ->body('Belge başarıyla silindi ve kullanıcıya bildirim gönderildi.')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('download')
                    ->label('İndir')
                    ->url(fn ($record) => $record->file_path ? \Storage::url($record->file_path) : null)
                    ->icon('heroicon-o-arrow-down-tray')
                    ->openUrlInNewTab()
                    ->color('success'),
                    Tables\Actions\Action::make('approve')
                    ->label('Onayla')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'approved',
                            'reviewed_at' => now(),
                            'reviewed_by' => Auth::id(),
                        ]);
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
                            'reviewed_by' => Auth::id(),
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
                                // Create a notification for the user about the document deletion
                                \App\Models\Notifications::create([
                                    'notifiable_id' => $record->user_id,
                                    'notifiable_type' => \App\Models\User::class,
                                    'title' => 'Belgeniz Silindi',
                                    'message' => 'Yüklediğiniz belge yönetici tarafından silindi.',
                                    'type' => 'document_rejected',
                                    'is_read' => false,
                                ]);
                                
                                // Delete the record
                                $record->delete();
                            }
                            
                            // Show notification to admin
                            \Filament\Notifications\Notification::make()
                                ->title('Belgeler Silindi')
                                ->body('Seçili belgeler başarıyla silindi ve kullanıcılara bildirim gönderildi.')
                                ->success()
                                ->send();
                        }),
                        Tables\Actions\BulkAction::make('approve_multiple')
                        ->label('Toplu Onayla')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update([
                                    'status' => 'approved',
                                    'reviewed_at' => now(),
                                    'reviewed_by' => Auth::id(),
                                ]);
                            }
                        }),
                ]),
            ]);
    }
    
} 
