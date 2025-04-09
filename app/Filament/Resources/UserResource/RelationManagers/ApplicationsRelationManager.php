<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
class ApplicationsRelationManager extends RelationManager
{
    protected static string $relationship = 'applications';
    protected static ?string $title = 'Başvurular';
    protected static ?string $breadcrumb = 'Başvurular';
    protected static ?string $breadcrumbParent = 'Kullanıcılar';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('program_id')
                    ->relationship('program', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('status')
                    ->options([
                        'scholarship_pool' => 'Burs Havuzu',
                        'pre_approved' => 'Ön Kabul',
                        'rejected' => 'Reddedildi',
                        'awaiting_documents' => 'Evrak Bekleniyor',
                        'documents_under_review' => 'Evrak İncelemede',
                        'interview_pool' => 'Mülakat Havuzu',
                        'awaiting_evaluation' => 'Değerlendirme Bekleniyor',
                        'interview_scheduled' => 'Mülakat Planlandı',
                        'interview_completed' => 'Mülakat Tamamlandı',
                        'accepted' => 'Kabul Edildi',
                        'final_acceptance' => 'Kesin Kabul',
                        'previous_scholar' => 'Önceki Burslu',
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('application_date')
                    ->label('Başvuru Tarihi')
                    ->required()
                    ->default(now()),
                Forms\Components\Toggle::make('are_documents_approved')
                    ->label('Evraklar Onaylandı'),
                Forms\Components\Toggle::make('is_interview_completed')
                    ->label('Mülakat Tamamlandı'),
                Forms\Components\Textarea::make('notes')
                    ->label('Notlar')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('rejection_reason')
                    ->label('Reddetme Nedeni')
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
        ->emptyStateHeading('Başvuru bulunamadı')
        ->emptyStateDescription('Yeni bir başvuru eklemek için "Yeni Başvuru" düğmesine tıklayın.')
        ->emptyStateActions([
            Tables\Actions\CreateAction::make()
                ->label('Yeni Başvuru')
        ])
        ->searchable()
        ->searchPlaceholder('Ara...')
        ->columns([
            Tables\Columns\ImageColumn::make('image')
                ->label('Profil')
                ->circular()
                ->defaultImageUrl(function ($record) {
                    $name = trim(($record->name ?? '') . ' ' . ($record->surname ?? ''));
                    if (empty($name)) $name = 'Kullanıcı';
                    return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&size=128&background=808080&color=ffffff&bold=true&length=2';
                }),
            Tables\Columns\TextColumn::make('name')
                ->label('Öğrenci Adı')
                ->formatStateUsing(fn ($record) => $record->user->name . ' ' . $record->user->surname)
                ->default(Auth::user()->name)
                ->searchable(),
            Tables\Columns\TextColumn::make('school_name')
                ->label('Okul Adı')
             
                ->searchable(),
                Tables\Columns\TextColumn::make('status')
                ->label('Durum')
                ->badge()
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    // English status values
                    'scholarship_pool' => 'Burs Havuzu',
                    'pre_approved' => 'Ön Kabul',
                    'rejected' => 'Reddedildi',
                    'awaiting_documents' => 'Evrak Bekleniyor',
                    'documents_under_review' => 'Evrak İncelemede',
                    'interview_pool' => 'Mülakat Havuzu',
                    'awaiting_evaluation' => 'Değerlendirme Bekleniyor',
                    'interview_scheduled' => 'Mülakat Planlandı',
                    'interview_completed' => 'Mülakat Tamamlandı',
                    'accepted' => 'Kabul Edildi',
                    'final_acceptance' => 'Kesin Kabul',
                    'previous_scholar' => 'Önceki Burslu',
                    

                    'scholarship_pool' => 'Burs Havuzu',
                    'pre_approved' => 'Ön Kabul',
                    'rejected' => 'Reddedildi',
                    'awaiting_documents' => 'Evrak Bekleniyor',
                    'documents_under_review' => 'Evrak İncelemede',
                    'interview_pool' => 'Mülakat Havuzu',
                    'awaiting_evaluation' => 'Değerlendirme Bekleniyor',
                    'interview_scheduled' => 'Mülakat Planlandı',
                    'interview_completed' => 'Mülakat Tamamlandı',
                    'accepted' => 'Kabul Edildi',
                    'final_acceptance' => 'Kesin Kabul',
                    'previous_scholar' => 'Önceki Burslu',
                    // Turkish status values
                
                    default => $state,
                })
                ->colors([
                    'danger' => fn ($state) => in_array($state, ['rejected', 'red_edildi', 'Reddedildi']),
                    'success' => fn ($state) => in_array($state, ['accepted', 'kabul_edildi', 'mulakat_tamamlandi', 'interview_completed', 'final_acceptance', 'previous_scholar', 'dogrulama_tamamlandi', 'Kabul Edildi', 'Mülakat Tamamlandı', 'Doğrulama Tamamlandı', 'Kesin Kabul', 'Önceki Burslu']),
                    'purple' => fn ($state) => in_array($state, ['interview_scheduled', 'interview_pool', 'interview_scheduled','mulakat_planlandi','mulakat_havuzu','mulakat_planlandi']),
                    'warning' => fn ($state) => in_array($state, ['awaiting_evaluation', 'belgeler_yuklendi', 'Değerlendirme Bekleniyor', 'Belgeler Yüklendi']),
                    'primary' => fn ($state) => in_array($state, ['scholarship_pool', 'pre_approved', 'awaiting_documents', 'burs_havuzu', 'on_kabul', 'Burs Havuzu', 'Ön Kabul', 'Evrak Bekleniyor']),
                    'secondary' => fn ($state) => in_array($state, ['documents_under_review', 'interview_scheduled', 'mulakat_planlandi', 'Evrak İncelemede', 'Mülakat Planlandı']),
                ]),
            
                
            Tables\Columns\TextColumn::make('school_department')
                ->label('Bölüm')
                ->searchable(),
            Tables\Columns\TextColumn::make('application_date')
                ->label('Başvuru Tarihi')
                ->dateTime('d.m.Y H:i')
                ->sortable(),


                
            Tables\Columns\TextColumn::make('documents_detail')
                ->label('Evrak Detayı')
                ->toggleable(isToggledHiddenByDefault: true)
                ->formatStateUsing(function ($record) {
                    try {
                        // Get program ID
                        $programId = $record->program_id;
                        
                        if (!$programId) {
                            return '<div class="text-sm text-red-500">Program bilgisi eksik</div>';
                        }
                        
                        // Get required documents
                        $requiredDocuments = \App\Models\ProgramDocumentRequirement::where('program_id', $programId)
                            ->with('documentType')
                            ->get();
                        
                        if ($requiredDocuments->isEmpty()) {
                            return '<div class="text-sm text-yellow-500">Evrak gereksinimi tanımlanmamış</div>';
                        }
                        
                        // User documents
                        $userDocuments = $record->documents()->with('documentType')->get();
                        
                        // Build HTML table for document status
                        $html = '<div class="text-xs">';
                        $html .= '<table class="w-full border-collapse">';
                        $html .= '<thead><tr>';
                        $html .= '<th class="border-b p-1 text-left">Belge Türü</th>';
                        $html .= '<th class="border-b p-1 text-center">Durumu</th>';
                        $html .= '</tr></thead>';
                        $html .= '<tbody>';
                        
                        foreach ($requiredDocuments as $requirement) {
                            if (!$requirement->documentType) continue;
                            
                            $docType = $requirement->documentType;
                            $userDoc = $userDocuments->first(function($doc) use ($docType) {
                                return $doc->document_type_id === $docType->id;
                            });
                            
                            $html .= '<tr>';
                            $html .= '<td class="border-b p-1 text-left">' . e($docType->name) . '</td>';
                            
                            if (!$userDoc) {
                                $html .= '<td class="border-b p-1 text-center"><span class="px-1 bg-gray-100 text-gray-800 rounded">Eksik</span></td>';
                            } else {
                                $statusClass = match($userDoc->status) {
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'approved' => 'bg-green-100 text-green-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                                
                                $statusText = match($userDoc->status) {
                                    'pending' => 'Beklemede',
                                    'approved' => 'Onaylı',
                                    'rejected' => 'Reddedildi',
                                    default => 'Bilinmiyor'
                                };
                                
                                $html .= '<td class="border-b p-1 text-center"><span class="px-1 rounded ' . $statusClass . '">' . $statusText . '</span></td>';
                            }
                            
                            $html .= '</tr>';
                        }
                        
                        $html .= '</tbody></table></div>';
                        return $html;
                        
                    } catch (\Exception $e) {
                        return '<div class="text-sm text-red-500">Hata: ' . e($e->getMessage()) . '</div>';
                    }
                })
                ->html(),

            Tables\Columns\TextColumn::make('interview_status')
                ->label('Mülakat Durumu')
                ->formatStateUsing(function ($record) {
                    $interview = $record->interviews()->first();
                    if (!$interview) {
                        return 'Mülakat yapılmadı';
                    }
                    
                    // Turkish translations for interview status
                    return match ($interview->status) {
                        'scheduled' => 'Planlandı',
                        'completed' => 'Tamamlandı',
                        'canceled' => 'İptal Edildi',
                        'rescheduled' => 'Yeniden Planlandı',
                        'no_show' => 'Katılım Olmadı',
                        'confirmed' => 'Onaylandı',
                        default => $interview->status,
                    };
                })
                ->badge()
                ->colors([
                    'primary' => fn ($state) => $state === 'Planlandı',
                    'success' => fn ($state) => $state === 'Tamamlandı' || $state === 'Onaylandı',
                    'danger' => fn ($state) => $state === 'İptal Edildi' || $state === 'Katılım Olmadı',
                    'warning' => fn ($state) => $state === 'Yeniden Planlandı',
                    'secondary' => fn ($state) => $state === 'Mülakat yapılmadı',
                ]),
            
            Tables\Columns\TextColumn::make('interview_detail')
                ->label('Mülakat Detayı')
                ->toggleable(isToggledHiddenByDefault: true)
                ->formatStateUsing(function ($record) {
                    try {
                        $interview = $record->interviews()->first();
                        
                        if (!$interview) {
                            return '<div class="text-sm text-gray-500">Mülakat kaydı bulunamadı</div>';
                        }
                        
                        // Get interviewer info
                        $interviewer = $interview->interviewer ? $interview->interviewer->name . ' ' . ($interview->interviewer->surname ?? '') : 'Bilinmiyor';
                        
                        // Build HTML for interview details
                        $html = '<div class="text-xs">';
                        $html .= '<table class="w-full border-collapse">';
                        
                        // Status
                        $statusClass = match($interview->status) {
                            'scheduled' => 'bg-blue-100 text-blue-800',
                            'completed' => 'bg-green-100 text-green-800',
                            'canceled' => 'bg-red-100 text-red-800',
                            'rescheduled' => 'bg-yellow-100 text-yellow-800',
                            'no_show' => 'bg-gray-100 text-gray-800',
                            'confirmed' => 'bg-green-100 text-green-800',
                            default => 'bg-gray-100 text-gray-800'
                        };
                        
                        $statusText = match($interview->status) {
                            'scheduled' => 'Planlandı',
                            'completed' => 'Tamamlandı',
                            'canceled' => 'İptal Edildi',
                            'rescheduled' => 'Yeniden Planlandı',
                            'no_show' => 'Katılım Olmadı',
                            'confirmed' => 'Onaylandı',
                            default => $interview->status,
                        };
                        
                        $html .= '<tr><td class="border-b p-1 font-medium">Durum:</td>';
                        $html .= '<td class="border-b p-1"><span class="px-1 rounded ' . $statusClass . '">' . $statusText . '</span></td></tr>';
                        
                        // Date
                        if ($interview->scheduled_date) {
                            $html .= '<tr><td class="border-b p-1 font-medium">Tarih:</td>';
                            $html .= '<td class="border-b p-1">' . $interview->scheduled_date->format('d.m.Y H:i') . '</td></tr>';
                        }
                        
                        // Interviewer
                        $html .= '<tr><td class="border-b p-1 font-medium">Mülakatçı:</td>';
                        $html .= '<td class="border-b p-1">' . e($interviewer) . '</td></tr>';
                        
                        // Location
                        if ($interview->location) {
                            $html .= '<tr><td class="border-b p-1 font-medium">Konum:</td>';
                            $html .= '<td class="border-b p-1">' . e($interview->location) . '</td></tr>';
                        }
                        
                        // Meeting link (if online)
                        if ($interview->meeting_link) {
                            $html .= '<tr><td class="border-b p-1 font-medium">Link:</td>';
                            $html .= '<td class="border-b p-1"><a href="' . e($interview->meeting_link) . '" target="_blank" class="text-blue-600 underline">Toplantı Linki</a></td></tr>';
                        }
                        
                        // Score (if interview is completed)
                        if ($interview->status === 'completed' && isset($interview->score)) {
                            $html .= '<tr><td class="border-b p-1 font-medium">Puan:</td>';
                            $html .= '<td class="border-b p-1">' . e($interview->score) . '</td></tr>';
                        }
                        
                        $html .= '</table></div>';
                        return $html;
                        
                    } catch (\Exception $e) {
                        return '<div class="text-sm text-red-500">Hata: ' . e($e->getMessage()) . '</div>';
                    }
                })
                ->html(),

           
           
                Tables\Columns\IconColumn::make('are_documents_approved')
                ->label('Evraklar Onaylı')
                ->boolean()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\IconColumn::make('is_interview_completed')
                ->label('Mülakat Tamamlandı')
                ->boolean()
                ->toggleable(isToggledHiddenByDefault: true),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Oluşturulma Tarihi')
                ->dateTime('d.m.Y H:i')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('updated_at')
                ->label('Güncellenme Tarihi')
                ->dateTime('d.m.Y H:i')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->filters([
     
            Tables\Filters\SelectFilter::make('program')
                ->label('Program')
                ->relationship('program', 'name'),
            Tables\Filters\Filter::make('documents_approved')
                ->label('Evrakları Onaylı')
                ->query(fn (Builder $query): Builder => $query->where('are_documents_approved', true))
                ->toggle(),
            Tables\Filters\Filter::make('interview_completed')
                ->label('Mülakatı Tamamlanmış')
                ->query(fn (Builder $query): Builder => $query->where('is_interview_completed', true))
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
            Tables\Actions\DeleteAction::make()
                ->color('danger')
                ->label('Sil')
                ->requiresConfirmation()
                ->modalHeading('Başvuru Silme')
                ->modalDescription('Bu başvuruyu silmek istediğinizden emin misiniz?')
                ->modalSubmitActionLabel('Evet, Sil')
                ->modalCancelActionLabel('İptal'),
            
            // Tables\Actions\Action::make('viewDocuments')
            //     ->label('Evrakları Görüntüle')
            //     ->icon('heroicon-o-document-text')
            //     ->color('success')
            //     ->url(fn (Applications $record): string => route('filament.admin.resources.documents.index', ['tableFilters[application_id][value]' => $record->id]))
            //     ->openUrlInNewTab(),
            
            // Yeni Eklenen Aksiyonlar
            Tables\Actions\Action::make('move_to_interview_pool')
                ->visible(false),
                
            // Evrak durumu ile ilgili aksiyonlar
            Tables\Actions\Action::make('check_documents')
                ->label('Evrak Durumu Kontrol')
                ->icon('heroicon-o-clipboard-document-list')
                ->color('warning')
                ->visible(fn (Applications $record): bool => !$record->are_documents_approved)
                ->action(function (Applications $record) {
                    // Program ID'sini al
                    $programId = $record->program_id;
                    
                    // Gerekli belgeleri kontrol et
                    $requiredDocTypes = \App\Models\ProgramDocumentRequirement::where('program_id', $programId)
                        ->pluck('document_type_id')
                        ->toArray();
                    
                    if (empty($requiredDocTypes)) {
                        Notification::make()
                            ->title('Program için evrak tanımlanmamış')
                            ->body('Bu program için gerekli evraklar tanımlanmamış.')
                            ->warning()
                            ->send();
                        return;
                    }
                    
                    // Kullanıcı belgelerini al
                    $userDocTypes = $record->documents()
                        ->where('status', 'approved')
                        ->pluck('document_type_id')
                        ->toArray();
                    
                    // Eksik belgeleri bul
                    $missingDocTypes = array_diff($requiredDocTypes, $userDocTypes);
                    
                    if (empty($missingDocTypes)) {
                        // Tüm belgeler tamamlanmış, durumu güncelle
                        $record->are_documents_approved = true;
                        $record->status = 'dogrulama_tamamlandi';
                        $record->save();
                        
                        Notification::make()
                            ->title('Evraklar Tam')
                            ->body('Tüm gerekli evraklar onaylanmış. Başvuru mülakat aşamasına geçebilir.')
                            ->success()
                            ->send();
                    } else {
                        // Eksik belgeler var, kullanıcıya bildir
                        $missingDocNames = \App\Models\DocumentType::whereIn('id', $missingDocTypes)
                            ->pluck('name')
                            ->join(', ');
                        
                        Notification::make()
                            ->title('Eksik Evraklar')
                            ->body("Aşağıdaki evraklar eksik veya onaylanmamış: $missingDocNames")
                            ->warning()
                            ->send();
                            
                        // Belgeler eksik, durumu güncelle
                        $record->status = 'awaiting_documents';
                        $record->save();
                    }
                }),
            
            Tables\Actions\Action::make('invite_to_interview')
                ->label(function (Applications $record) {
                    // Eğer zaten mülakata aktarılmış veya planlama bekleyen bir mülakat varsa
                    if ($record->status === 'mulakat_havuzu' || 
                        \App\Models\Interviews::where('application_id', $record->id)
                            ->where('status', 'awaiting_schedule')
                            ->exists()) {
                        return 'Mülakata Aktarıldı ✓';
                    }
                    
                    return 'Mülakat Planla';
                })
                ->icon('heroicon-o-calendar')
                ->color(function (Applications $record) {
                    // Eğer zaten mülakata aktarılmışsa yeşil göster
                    if ($record->status === 'mulakat_havuzu' || 
                        \App\Models\Interviews::where('application_id', $record->id)
                            ->where('status', 'awaiting_schedule')
                            ->exists()) {
                        return 'success';
                    }
                    
                    return 'primary';
                })
                ->visible(fn (Applications $record): bool => $record->are_documents_approved && !$record->is_interview_scheduled)
                ->disabled(function (Applications $record) {
                    // Eğer zaten mülakata aktarılmışsa veya planlama bekleyen bir mülakat varsa devre dışı bırak
                    return $record->status === 'mulakat_havuzu' || 
                           \App\Models\Interviews::where('application_id', $record->id)
                            ->where('status', 'awaiting_schedule')
                            ->exists();
                })
                ->form([
                    Forms\Components\DateTimePicker::make('scheduled_date')
                        ->label('Mülakat Tarihi ve Saati')
                        ->required()
                        ->minDate(now())
                        ->seconds(false)
                        ->displayFormat('d/m/Y H:i')
                        ->native(false),
                    Forms\Components\Select::make('interviewer_id')
                        ->label('Mülakatçı')
                        ->options(
                            \App\Models\User::query()
                                ->where('is_admin', true)
                                ->get()
                                ->pluck('name', 'id')
                                ->toArray()
                        )
                        ->required()
                        ->searchable(),
                    Forms\Components\TextInput::make('location')
                        ->label('Konum')
                        ->placeholder('Örn: Ana Bina, Oda 203'),
                    Forms\Components\Toggle::make('is_online')
                        ->label('Online Mülakat mı?')
                        ->default(false)
                        ->reactive()
                        ->afterStateUpdated(function ($state, $set) {
                            if (!$state) {
                                $set('meeting_link', null); // Link alanını temizle
                            }
                        }),
                    Forms\Components\TextInput::make('meeting_link')
                        ->label('Toplantı Linki')
                        ->prefix('https://')
                        ->placeholder('Zoom veya Google Meet linki')
                        ->visible(fn (callable $get) => $get('is_online'))
                        ->dehydrateStateUsing(function ($state) {
                            if (empty($state)) {
                                return null;
                            }
                            
                            // URL'e http veya https ön eki yoksa ekle
                            if (!preg_match('~^(?:f|ht)tps?://~i', $state)) {
                                return 'https://' . $state;
                            }
                            
                            return $state;
                        }),
                    Forms\Components\Textarea::make('notes')
                        ->label('Notlar')
                        ->maxLength(65535),
                ])
                ->action(function (Applications $record, array $data) {
                    // Önce mülakata aktarma işlemini gerçekleştir (eğer henüz aktarılmamışsa)
                    if ($record->status !== 'mulakat_havuzu') {
                        // Başvurunun durumunu mülakata aktarılmış olarak güncelle
                        $record->status = 'mulakat_havuzu';
                        $record->save();
                        
                        // Mülakat kaydı oluştur (awaiting_schedule durumunda)
                        \App\Models\Interviews::create([
                            'application_id' => $record->id,
                            'user_id' => $record->user_id,
                            'status' => 'awaiting_schedule',
                            'created_at' => now(),
                            'interview_date' => now()->addDay(), // Add interview_date field with a default value
                        ]);
                        
                        Notification::make()
                            ->title('Başvuru mülakata aktarıldı')
                            ->success()
                            ->send();
                    }
                    
                    // Mülakatı planla
                    $interview = \App\Models\Interviews::where('application_id', $record->id)
                        ->where('status', 'awaiting_schedule')
                        ->first();
                    
                    if (!$interview) {
                        // Eğer bir şekilde interview kaydı yoksa yeni oluştur
                        $interview = \App\Models\Interviews::create([
                            'application_id' => $record->id,
                            'user_id' => $record->user_id,
                            'status' => 'awaiting_schedule',
                            'created_at' => now(),
                            'interview_date' => now()->addDay(), // Add interview_date field with a default value
                        ]);
                    }
                    
                    // Mülakat bilgilerini güncelle
                    $interview->interviewer_admin_id = $data['interviewer_id'];
                    $interview->scheduled_date = $data['scheduled_date'];
                    $interview->interview_date = $data['scheduled_date'];
                    $interview->location = $data['location'] ?? null;
                    $interview->is_online = $data['is_online'] ?? false;
                    $interview->meeting_link = $data['meeting_link'] ?? null;
                    $interview->notes = $data['notes'] ?? null;
                    $interview->status = 'scheduled';
                    $interview->save();
                    
                    // Başvuru durumunu güncelle
                    $record->is_interview_scheduled = true;
                    $record->status = 'interview_scheduled';
                    $record->interview_pool_at = now();
                    $record->interview_pool_by = auth()->id();
                    $record->save();
                    
                    Notification::make()
                        ->title('Mülakat başarıyla planlandı')
                        ->success()
                        ->send();
                })
                ->modalHeading('Mülakat Planla')
                ->modalDescription('Lütfen mülakat detaylarını girin')
                ->modalSubmitActionLabel('Planla'),
            
            Tables\Actions\Action::make('complete_interview')
                ->label('Mülakatı Tamamla')
                ->icon('heroicon-o-check-badge')
                ->color('primary')
                ->visible(fn (Applications $record): bool => $record->is_interview_scheduled && !$record->is_interview_completed)
                ->form([
                    Forms\Components\Select::make('result')
                        ->label('Mülakat Sonucu')
                        ->options([
                            'passed' => 'Başarılı',
                            'failed' => 'Başarısız',
                        ])
                        ->required(),
                    Forms\Components\TextInput::make('score')
                        ->label('Mülakat Puanı (0-100)')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->required(),
                    Forms\Components\Textarea::make('feedback')
                        ->label('Geri Bildirim')
                        ->maxLength(65535)
                        ->required(),
                ])
                ->action(function (Applications $record, array $data) {
                    // En son mülakatı bul ve güncelle
                    $interview = \App\Models\Interviews::where('application_id', $record->id)
                        ->where('status', 'scheduled')
                        ->latest()
                        ->first();
                        
                    if ($interview) {
                        $interview->status = 'completed';
                        $interview->interview_result = $data['result'];
                        $interview->interview_score = $data['score'];
                        $interview->feedback = $data['feedback'];
                        $interview->completion_date = now();
                        $interview->save();
                    }
                    
                    // Başvuru durumunu güncelle
                    $record->status = $data['result'] === 'passed' ? 'mulakat_tamamlandi' : 'reddedildi';
                    $record->is_interview_completed = true;
                    $record->interview_result = $data['result'];
                    $record->interview_score = $data['score'];
                    $record->save();
                    
                    // Bildirim göster
                    \Filament\Notifications\Notification::make()
                        ->title($data['result'] === 'passed' ? 'Mülakat Başarılı' : 'Mülakat Başarısız')
                        ->body($data['result'] === 'passed' ? 'Mülakat başarıyla tamamlandı.' : 'Mülakat başarısız oldu.')
                        ->color($data['result'] === 'passed' ? 'success' : 'danger')
                        ->send();
                }),
            
            Tables\Actions\Action::make('transfer_scholarship')
                ->label('Bursa Aktar')
                ->icon('heroicon-o-currency-dollar')
                ->color('success')
                ->button() // Buton olarak görünsün
                ->visible(fn (Applications $record): bool => 
                    $record->are_documents_approved && 
                    $record->is_interview_completed && 
                    $record->interview_result === 'passed' &&
                    !$record->scholarships()->exists())
                ->form([
                    Forms\Components\Select::make('scholarship_amount')
                        ->label('Burs Miktarı (₺)')
                        ->options([
                            '500' => '500 ₺',
                            '750' => '750 ₺',
                            '1000' => '1000 ₺',
                            '1500' => '1500 ₺',
                            '2000' => '2000 ₺',
                            '2500' => '2500 ₺',
                            '3000' => '3000 ₺',
                            '3500' => '3500 ₺',
                            '4000' => '4000 ₺',
                            '5000' => '5000 ₺',
                        ])
                        ->required(),
                    Forms\Components\DatePicker::make('scholarship_start_date')
                        ->label('Burs Başlangıç Tarihi')
                        ->required()
                        ->minDate(now()),
                    Forms\Components\DatePicker::make('scholarship_end_date')
                        ->label('Burs Bitiş Tarihi')
                        ->required()
                        ->minDate(function ($get) {
                            $startDate = $get('scholarship_start_date');
                            return $startDate ? \Illuminate\Support\Carbon::parse($startDate)->addMonths(1) : now()->addMonths(1);
                        }),
                    Forms\Components\Textarea::make('notes')
                        ->label('Notlar')
                        ->maxLength(65535),
                ])
                ->action(function (Applications $record, array $data) {
                    // Başvuru durumunu güncelle
                    $record->status = 'kabul_edildi';
                    $record->approval_date = now();
                    $record->approval_notes = $data['notes'] ?? null;
                    $record->scholarship_amount = $data['scholarship_amount'];
                    $record->scholarship_start_date = $data['scholarship_start_date'];
                    $record->scholarship_end_date = $data['scholarship_end_date'];
                    $record->save();
                    
                    // Burs kaydı oluştur
                    \App\Models\Scholarships::create([
                        'user_id' => $record->user_id,
                        'program_id' => $record->program_id,
                        'application_id' => $record->id,
                        'approved_by' => auth()->id(),
                        'name' => 'Standart Burs',
                        'start_date' => \Carbon\Carbon::parse($data['scholarship_start_date'])->format('Y-m-d'),
                        'end_date' => \Carbon\Carbon::parse($data['scholarship_end_date'])->format('Y-m-d'),
                        'amount' => (float) $data['scholarship_amount'],
                        'status' => 'active',
                        'notes' => $data['notes'] ?? null,
                    ]);
                    
                    // Bildirimi göster
                    \Filament\Notifications\Notification::make()
                        ->title('Burs Kaydı Oluşturuldu')
                        ->body('Başvuru için burs kaydı başarıyla oluşturuldu.')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->modalHeading('Bursa Aktar')
                ->modalDescription('Bu başvuru için burs kaydı oluşturmak üzeresiniz.')
                ->modalSubmitActionLabel('Burs Kaydı Oluştur'),
            

                
            Tables\Actions\Action::make('reject_application')
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
                    $record->rejection_reason = $data['rejection_reason'];
                    $record->rejected_by = auth()->id();
                    $record->rejected_at = now();
                    $record->save();
                    
                    // Create a notification for the user about the rejection
                    \App\Models\Notifications::create([
                        'notifiable_id' => $record->user_id,
                        'notifiable_type' => \App\Models\User::class,
                        'title' => 'Başvurunuz Reddedildi',
                        'message' => 'Başvurunuz değerlendirilmiş ve reddedilmiştir. Red nedeni: ' . $data['rejection_reason'],
                        'type' => 'application_status',
                        'application_id' => $record->id,
                        'is_read' => false,
                    ]);
                    
                    \Filament\Notifications\Notification::make()
                        ->title('Başvuru Reddedildi')
                        ->body('Başvuru reddedildi.')
                        ->danger()
                        ->send();
                })
                ->requiresConfirmation()
                ->modalHeading('Başvuru reddedilsin mi?')
                ->modalDescription('Bu başvuruyu reddetmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')
                ->modalSubmitActionLabel('Evet, Reddet'),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make()
                    ->label('Sil'),
              
                // Yeni eklenen toplu aksiyonlar
                Tables\Actions\BulkAction::make('bulk_check_documents')
                    ->label('Evrak Kontrol')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->color('warning')
                    ->deselectRecordsAfterCompletion()
                    ->action(function (Collection $records) {
                        $success = 0;
                        $missing = 0;
                        $noRequirements = 0;
                        
                        foreach ($records as $record) {
                            // Program ID'sini al
                            $programId = $record->program_id;
                            
                            // Gerekli belgeleri kontrol et
                            $requiredDocTypes = \App\Models\ProgramDocumentRequirement::where('program_id', $programId)
                                ->pluck('document_type_id')
                                ->toArray();
                            
                            if (empty($requiredDocTypes)) {
                                $noRequirements++;
                                continue; // Bu program için belge gereksinimleri bulunmadığından bir sonraki kayda geç
                            }
                            
                            // Kullanıcı belgelerini al
                            $userDocTypes = $record->documents()
                                ->where('status', 'approved')
                                ->pluck('document_type_id')
                                ->toArray();
                            
                            // Eksik belgeleri bul
                            $missingDocTypes = array_diff($requiredDocTypes, $userDocTypes);
                            
                            if (empty($missingDocTypes)) {
                                // Tüm belgeler tamamlanmış, durumu güncelle
                                $record->are_documents_approved = true;
                                $record->status = 'dogrulama_tamamlandi';
                                $record->save();
                                $success++;
                            } else {
                                // Belgeler eksik, durumu güncelle
                                $record->status = 'awaiting_documents';
                                $record->save();
                                $missing++;
                            }
                        }
                        
                        $message = "$success başvuru için evraklar tam, $missing başvuru için eksik evrak bulundu.";
                        if ($noRequirements > 0) {
                            $message .= " $noRequirements başvuru için evrak gereksinimleri tanımlanmamış.";
                        }
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Evrak Kontrolü Tamamlandı')
                            ->body($message)
                            ->success()
                            ->send();
                    }),
                
                Tables\Actions\BulkAction::make('bulk_move_to_interview_pool')
                    ->label('Toplu Mülakata Aktar')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('primary')
                    ->deselectRecordsAfterCompletion()
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        $moved = 0;
                        $notMoved = 0;
                        
                        foreach ($records as $record) {
                            if ($record->are_documents_approved && !$record->is_interview_scheduled) {
                                $record->status = 'mulakat_havuzu';
                                $record->save();
                                
                                // Ön bir mülakat kaydı oluşturalım ki InterviewManagementResource'da görünsün
                                \App\Models\Interviews::create([
                                    'application_id' => $record->id,
                                    'user_id' => $record->user_id,
                                    'interviewer_admin_id' => auth()->id(), // Şimdilik oluşturan kişi
                                    'status' => 'awaiting_schedule', // Özel durum: Henüz planlanmamış
                                    'created_at' => now(),
                                    'interview_date' => now()->addDay(), // Add interview_date field with a default value
                                    'notes' => 'Bu mülakat henüz planlanmamıştır. Lütfen planlamayı yapın.'
                                ]);
                                
                                $moved++;
                            } else {
                                $notMoved++;
                            }
                        }
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Mülakata Aktarıldı')
                            ->body("$moved başvuru mülakat havuzuna aktarıldı. $notMoved başvuru için evrak onayı eksik veya zaten mülakata aktarılmış.")
                            ->success()
                            ->send();
                    }),
                    
                Tables\Actions\BulkAction::make('bulk_reject')
                    ->label('Toplu Reddet')
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
                            $record->rejection_reason = $data['rejection_reason'];
                            $record->rejected_by = auth()->id();
                            $record->rejected_at = now();
                            $record->save();
                            
                            // Create a notification for the user about the rejection
                            \App\Models\Notifications::create([
                                'notifiable_id' => $record->user_id,
                                'notifiable_type' => \App\Models\User::class,
                                'title' => 'Başvurunuz Reddedildi',
                                'message' => 'Başvurunuz değerlendirilmiş ve reddedilmiştir. Red nedeni: ' . $data['rejection_reason'],
                                'type' => 'application_status',
                                'application_id' => $record->id,
                                'is_read' => false,
                            ]);
                        }
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Başvurular Reddedildi')
                            ->body(count($records) . ' başvuru reddedildi.')
                            ->danger()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Başvurular reddedilsin mi?')
                    ->modalDescription('Seçili başvuruları reddetmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')
                    ->modalSubmitActionLabel('Evet, Reddet'),
            ]),
        ]);
    }
} 