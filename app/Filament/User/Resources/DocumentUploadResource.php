<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\DocumentUploadResource\Pages;
use App\Filament\User\Resources\DocumentUploadResource\RelationManagers;
use App\Models\Documents;
use App\Models\DocumentType;
use App\Models\Applications;
use App\Models\ProgramDocumentRequirement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class DocumentUploadResource extends Resource
{
    protected static ?string $model = Documents::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-up';
    
    protected static ?string $navigationLabel = 'Evrak Yükleme';
    
    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Burs İşlemleri';

    protected static ?string $title = 'Evrak Yükleme';

    protected static ?string $breadcrumb = 'Evrak Yükleme';


    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }

    public static function form(Form $form): Form
    {
        $applicationId = request()->get('application_id');
        $programId = null;
        
        if ($applicationId) {
            $application = Applications::find($applicationId);
            if ($application) {
                $programId = $application->program_id;
            }
        }
        
        return $form
            ->schema([
                Forms\Components\Section::make('Evrak Bilgileri')
                    ->schema([
                        Forms\Components\Hidden::make('user_id')
                            ->default(fn () => Auth::id()),
                        Forms\Components\Hidden::make('application_id')
                            ->default($applicationId),
                        Forms\Components\Select::make('document_type_id')
                            ->label('Belge Türü')
                            ->options(function () use ($programId) {
                                if ($programId) {
                                    $requiredDocs = ProgramDocumentRequirement::where('program_id', $programId)
                                        ->with('documentType')
                                        ->get()
                                        ->pluck('documentType.name', 'document_type_id');
                                    
                                    return $requiredDocs;
                                }
                                
                                return DocumentType::pluck('name', 'id');
                            })
                            ->required()
                            ->searchable(),
                        Forms\Components\TextInput::make('name')
                            ->label('Belge Adı')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\FileUpload::make('file_path')
                            ->label('Dosya')
                            ->required()
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/gif'])
                            ->disk('public')
                            ->directory('documents')
                            ->visibility('public')
                            ->maxSize(5120) // 5MB
                            ->imagePreviewHeight('250')
                            ->loadingIndicatorPosition('left')
                            ->panelAspectRatio('2:1')
                            ->panelLayout('integrated')
                            ->removeUploadedFileButtonPosition('right')
                            ->uploadProgressIndicatorPosition('left'),
                        Forms\Components\Textarea::make('description')
                            ->label('Açıklama')
                            ->maxLength(65535),
                        Forms\Components\Hidden::make('status')
                            ->default('beklemede'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->emptyStateHeading('Evrak yüklemeniz Yok')
        ->emptyStateDescription('Herhangi bir evrak yüklemeniz bulunamadı.')
        ->emptyStateActions([
            Tables\Actions\CreateAction::make('evrak_yukle')
                ->label('Evrak Yükle')
                ->icon('heroicon-o-document-arrow-up')
                ->color('success'),
        ])
            ->columns([
                Tables\Columns\ImageColumn::make('file_path')
                    ->label('Belge')
                    ->height(20)
                    ->disk('public')
                    ->visibility('public'),
                Tables\Columns\TextColumn::make('documentType.name')
                    ->label('Belge Türü')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Belge Adı')
                    ->searchable(),
                // Tables\Columns\IconColumn::make('is_verified')
                //     ->label('Doğrulandı')
                //     ->boolean()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Yükleme Tarihi')
                    ->date()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('verification_date')
                //     ->label('Doğrulama Tarihi')
                //     ->date()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'beklemede' => 'warning',
                        'onaylandi' => 'success',
                        'reddedildi' => 'danger',
                        default => 'warning',
                    })
                    ->sortable(),
                // Tables\Columns\TextColumn::make('application.program.name')
                //     ->label('Burs Programı')
                //     ->badge()
                //     ->default('Burs Programı')
                //     ->color('success')
                //     ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_verified')
                    ->label('Doğrulama Durumu')
                    ->options([
                        true => 'Doğrulandı',
                        false => 'Doğrulanmadı',
                    ]),
                Tables\Filters\SelectFilter::make('document_type_id')
                    ->label('Belge Türü')
                    ->options(function() {
                        return DocumentType::pluck('name', 'id');
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Görüntüle'),
                // Tables\Actions\Action::make('download')
                //     ->label('İndir')
                //     ->icon('heroicon-o-arrow-down-tray')
                //     ->url(fn (Documents $record): string => secure_asset('storage/' . $record->file_path))
                //     ->openUrlInNewTab(),
                // Tables\Actions\DeleteAction::make()
                //     ->label('Sil')
                //     ->icon('heroicon-o-trash')
                //     ->color('danger'),
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListDocumentUploads::route('/'),
            'create' => Pages\CreateDocumentUpload::route('/create'),
            'view' => Pages\ViewDocumentUpload::route('/{record}'),
            'edit' => Pages\EditDocumentUpload::route('/{record}/edit'),
        ];
    }
}
