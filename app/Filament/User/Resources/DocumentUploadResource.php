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
use Illuminate\Support\HtmlString;

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
                            ->reactive()
                            ->uploadingMessage('Yükleniyor...')
                            ->placeholder('Dosyayı sürükleyin veya yükleyin')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                            ->helperText('Yalnızca PDF, JPEG, PNG formatındaki dosyaları yükleyebilirsiniz.')
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
                            ->default('pending'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        $user = Auth::user();
        $latestApplication = Applications::where('user_id', $user->id)
            ->whereNotIn('status', ['reddedildi', 'red_edildi', 'rejected'])
            ->latest()
            ->first();
        
        $programId = $latestApplication ? $latestApplication->program_id : null;
        
        // Program için gerekli belgeleri bulalım
        $requiredDocuments = collect([]);
        if ($programId) {
            $requiredDocuments = ProgramDocumentRequirement::where('program_id', $programId)
                ->with('documentType')
                ->get();
        }
        
        // Kullanıcının yüklediği belgeleri bulalım
        $uploadedDocuments = Documents::where('user_id', $user->id)
            ->when($latestApplication, function($query) use ($latestApplication) {
                return $query->where('application_id', $latestApplication->id);
            })
            ->get()
            ->pluck('document_type_id')
            ->toArray();
        
        // Eksik belgeleri bulalım
        $missingDocuments = $requiredDocuments->filter(function($requirement) use ($uploadedDocuments) {
            return $requirement->is_required && !in_array($requirement->document_type_id, $uploadedDocuments);
        });
        
        // Başvuru için tüm zorunlu belgeler yüklenmişse otomatik olarak durumu güncelleyelim
        if ($latestApplication && $missingDocuments->isEmpty() && $requiredDocuments->isNotEmpty()) {
            // Eksik belge kalmadıysa başvuruyu güncelle
            if ($latestApplication->status == 'beklemede' || $latestApplication->status == 'awaiting_documents') {
                $latestApplication->status = 'belgeler_yuklendi';
                $latestApplication->save();
            }
        }
        
        return $table
            ->emptyStateHeading('Evrak yüklemeniz Yok')
            ->emptyStateDescription('Herhangi bir evrak yüklemeniz bulunamadı.')
            ->headerActions([
                // Program için gerekli belgeleri gösterme butonu ekleyeceğiz
                Tables\Actions\Action::make('requiredDocuments')
                ->label('Gerekli Belgeler')
                ->icon('heroicon-o-document-text')
                ->color('primary')
                ->modalWidth('md')
                ->modalHeading('Yüklenmesi Gereken Belgeler')
                ->modalContent(function() use ($requiredDocuments, $uploadedDocuments) {
                    // Belgeleri tiplerine göre grupla ve eksik olanları vurgula
                    $documentTypes = $requiredDocuments->groupBy('document_type_id');
                    
                    $content = '<div class="space-y-5 p-2">';
                    
                    if ($documentTypes->isEmpty()) {
                        // Tüm belge türlerini gösterelim, program için belge gerekliliği olmasa bile
                        $allDocumentTypes = DocumentType::where('is_active', true)->get();
                        
                        if ($allDocumentTypes->isEmpty()) {
                            $content .= '<div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg text-center">
                                <p class="text-gray-500 dark:text-gray-400">Henüz belge türü tanımlanmamış.</p>
                            </div>';
                        } else {
                            foreach ($allDocumentTypes as $documentType) {
                                $isUploaded = in_array($documentType->id, $uploadedDocuments);
                                
                                $content .= '<div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden mb-4">';
                                $content .= '<div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 border-b border-gray-200 dark:border-gray-700">';
                                $content .= '<h3 class="font-medium text-gray-900 dark:text-white">' . $documentType->name . '</h3>';
                                $content .= '</div>';
                                
                                $content .= '<div class="px-4 py-3 flex items-center justify-between">';
                                
                                // Belge adı ve gereklilik durumu
                                $content .= '<div class="flex items-center gap-x-2">';
                                
                                if ($documentType->is_required) {
                                    $content .= '<span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Zorunlu</span>';
                                } else {
                                    $content .= '<span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">İsteğe Bağlı</span>';
                                }
                                $content .= '</div>';
                                
                                // Yüklenme durumu
                                $content .= '<div>';
                                if ($isUploaded) {
                                    $content .= '<span class="inline-flex items-center px-2.5 py-1 rounded-full bg-green-100 dark:bg-green-800 text-sm font-medium text-green-800 dark:text-green-100">';
                                    $content .= '<svg class="w-5 h-5 mr-1.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>';
                                    $content .= 'Yüklendi</span>';
                                } else {
                                    $content .= '<span class="inline-flex items-center px-2.5 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-sm font-medium text-gray-800 dark:text-gray-300">';
                                    $content .= '<svg class="w-5 h-5 mr-1.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>';
                                    $content .= 'Yüklenmedi</span>';
                                }
                                $content .= '</div>';
                                
                                $content .= '</div>';
                                $content .= '</div>';
                            }
                            
                            // Eksik belgelere özel uyarı mesajı
                            $missingCount = $allDocumentTypes->filter(function($doc) use ($uploadedDocuments) {
                                return !in_array($doc->id, $uploadedDocuments);
                            })->count();
                            
                            if ($missingCount > 0) {
                                $content .= '<div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">';
                                $content .= '<div class="flex items-center">';
                                $content .= '<svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z" clip-rule="evenodd"></path></svg>';
                                $content .= '<span class="font-medium text-blue-800">' . $missingCount . ' adet belge henüz yüklenmemiş.</span>';
                                $content .= '</div>';
                                $content .= '</div>';
                            }
                        }
                    } else {
                        foreach ($documentTypes as $typeId => $requirements) {
                            $typeName = $requirements->first()->documentType->name;
                            $content .= '<div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">';
                            $content .= '<div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 border-b border-gray-200 dark:border-gray-700">';
                            $content .= '<h3 class="font-medium text-gray-900 dark:text-white">' . $typeName . '</h3>';
                            $content .= '</div>';
                            
                            $content .= '<div class="divide-y divide-gray-200 dark:divide-gray-700">';
                            
                            foreach ($requirements as $requirement) {
                                $isUploaded = in_array($requirement->document_type_id, $uploadedDocuments);
                                
                                $content .= '<div class="px-4 py-3 flex items-center justify-between">';
                                
                                // Belge adı ve gereklilik durumu
                                $content .= '<div class="flex items-center gap-x-2">';
                                $content .= '<span class="font-medium text-gray-900 dark:text-white">' . $requirement->documentType->name . '</span>';
                                
                                if ($requirement->is_required) {
                                    $content .= '<span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Zorunlu</span>';
                                } else {
                                    $content .= '<span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">İsteğe Bağlı</span>';
                                }
                                $content .= '</div>';
                                
                                // Yüklenme durumu
                                $content .= '<div>';
                                if ($isUploaded) {
                                    $content .= '<span class="inline-flex items-center px-2.5 py-1 rounded-full bg-green-100 dark:bg-green-800 text-sm font-medium text-green-800 dark:text-green-100">';
                                    $content .= '<svg class="w-5 h-5 mr-1.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>';
                                    $content .= 'Yüklendi</span>';
                                } else {
                                    $content .= '<span class="inline-flex items-center px-2.5 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-sm font-medium text-gray-800 dark:text-gray-300">';
                                    $content .= '<svg class="w-5 h-5 mr-1.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>';
                                    $content .= 'Yüklenmedi</span>';
                                }
                                $content .= '</div>';
                                
                                $content .= '</div>';
                            }
                            
                            $content .= '</div>';
                            $content .= '</div>';
                        }
                        
                        // Eksik belgelere özel uyarı mesajı
                        $missingRequiredCount = $requiredDocuments->filter(function($doc) use ($uploadedDocuments) {
                            return $doc->is_required && !in_array($doc->document_type_id, $uploadedDocuments);
                        })->count();
                        
                        if ($missingRequiredCount > 0) {
                            $content .= '<div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">';
                            $content .= '<div class="flex items-center">';
                            $content .= '<svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>';
                            $content .= '<span class="font-medium text-yellow-800">' . $missingRequiredCount . ' adet zorunlu belge eksik. Lütfen eksik belgeleri yükleyiniz.</span>';
                            $content .= '</div>';
                            $content .= '</div>';
                        }
                    }
                    
                    $content .= '</div>';
                    
                    return new HtmlString($content);
                })
                ->modalSubmitAction(false)
                    ->modalCancelAction(false),
                    
                Tables\Actions\CreateAction::make()
                    ->label('Belge Yükle')
                    ->icon('heroicon-o-document-plus'),
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
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Yükleme Tarihi')
                    ->date()
                    ->sortable(),
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
