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
use App\Models\Applications;
use App\Models\ProgramDocumentRequirement;
use App\Models\DocumentType;
use Filament\Notifications\Notification;

class DocumentResource extends Resource
{
    protected static ?string $model = Documents::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationGroup = 'Belge Yönetimi';
    
    protected static ?int $navigationSort = 4;
    
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
                            ->placeholder('Yüklemek istediğiniz dosyayı seçiniz')
                            ->required()
                            ->disk('public')
                            ->directory('documents')
                            ->visibility('private')
                            ->image()
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
                    ->successNotification(
                        Notification::make()
                            ->title('Yeni Belgeler Oluşturuldu')
                            ->body('Yeni belgeler başarıyla oluşturuldu.')
                            ->success()
                    ),
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
                    ->label('Belge Türü')
                    ->relationship('documentType', 'name')
                    ->searchable(),
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
                    ->modalHeading('Belge Onayı')
                    ->modalDescription('Bu belgeyi onaylamak istediğinizden emin misiniz?')
                    ->modalCancelActionLabel('İptal')
                    ->modalSubmitActionLabel('Evet, Onayla')
                    ->modalSubheading('Bu işlemi onaylamak istediğinizden emin misiniz?')
                    ->modalWidth('sm')
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'approved',
                            'reviewed_at' => now(),
                            'reviewed_by' => auth()->id(),
                        ]);
                        
                        // Our application observer will automatically update the are_documents_approved flag
                        // and the document model's boot method will trigger the checkDocumentApprovalStatus method
                        
                        if ($record->application_id) {
                            $application = \App\Models\Applications::find($record->application_id);
                            if ($application) {
                                // Show notification
                                \Filament\Notifications\Notification::make()
                                    ->title('Belge Onaylandı')
                                    ->body('Belge başarıyla onaylandı.')
                                    ->success()
                                    ->send();
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
                        ->label('Toplu Belge Silme')
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
                ])

         
                ->label('Belge İşlemleri'),
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
