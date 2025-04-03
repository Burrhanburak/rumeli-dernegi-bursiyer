<?php

namespace App\Filament\Resources\ScholarshipProgramResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Applications;

class ApplicationsRelationManager extends RelationManager
{
    protected static string $relationship = 'applications';
    
    protected static ?string $title = 'Başvurular';
    protected static ?string $breadcrumb = 'Başvurular';
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name', function ($query) {
                        return $query->where('is_admin', false);
                    })
                    ->required()
                    ->searchable()
                    ->label('Başvuran'),
                    
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
                        'previous_scholar' => 'Önceki Burslu'
                    ])
                    ->required()
                    ->label('Durum'),
                    
                Forms\Components\DatePicker::make('application_date')
                    ->default(now())
                    ->label('Başvuru Tarihi'),
                    
                Forms\Components\Textarea::make('rejection_reason')
                    ->maxLength(65535)
                    ->label('Red Nedeni')
                    ->visible(fn (callable $get) => $get('status') === 'rejected'),
                    
                Forms\Components\Textarea::make('notes')
                    ->maxLength(65535)
                    ->label('Notlar')
                    ->columnSpanFull(),
                    
                Forms\Components\Select::make('reviewed_by')
                    ->relationship('reviewer', 'name')
                    ->label('İnceleyen')
                    ->searchable()
                    ->visible(fn (callable $get) => in_array($get('status'), [
                        'documents_under_review', 
                        'interview_pool',
                        'interview_scheduled',
                        'interview_completed',
                        'accepted',
                        'final_acceptance'
                    ])),
                    
                Forms\Components\DatePicker::make('reviewed_at')
                    ->label('İnceleme Tarihi')
                    ->visible(fn (callable $get) => in_array($get('status'), [
                        'documents_under_review', 
                        'interview_pool',
                        'interview_scheduled',
                        'interview_completed',
                        'accepted',
                        'final_acceptance'
                    ])),
            ]);
    }
    
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->emptyStateHeading('Bu programa ait başvuru bulunamadı')
            ->emptyStateDescription('Programa ait başvuruları görüntülemek için önce bir başvuru oluşturmalısınız.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Yeni Başvuru')
                    ->icon('heroicon-o-plus'),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->label('ID'),
                    
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Başvuran')
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->user) {
                            return $record->user->name . ' (' . $record->user->email . ')';
                        }
                        return $state;
                    })
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
                        'purple' => fn ($state) => in_array($state, ['interview_scheduled', 'interview_pool', 'interview_scheduled']),
                        'warning' => fn ($state) => in_array($state, ['awaiting_evaluation', 'belgeler_yuklendi', 'Değerlendirme Bekleniyor', 'Belgeler Yüklendi']),
                        'primary' => fn ($state) => in_array($state, ['scholarship_pool', 'pre_approved', 'awaiting_documents', 'burs_havuzu', 'on_kabul', 'Burs Havuzu', 'Ön Kabul', 'Evrak Bekleniyor']),
                        'secondary' => fn ($state) => in_array($state, ['documents_under_review', 'interview_scheduled', 'mulakat_planlandi', 'Evrak İncelemede', 'Mülakat Planlandı']),
                    ]),
                
                Tables\Columns\TextColumn::make('application_date')
                    ->dateTime()
                    ->sortable()
                    ->label('Başvuru Tarihi'),
                    
                Tables\Columns\TextColumn::make('reviewer.name')
                    ->label('İnceleyen')
                    ->searchable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('reviewed_at')
                    ->dateTime()
                    ->sortable()
                    ->label('İnceleme Tarihi')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Oluşturulma Tarihi'),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Güncellenme Tarihi'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
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
                        'previous_scholar' => 'Önceki Burslu'
                    ])
                    ->label('Durum'),
                    
                Tables\Filters\Filter::make('application_date')
                    ->form([
                        Forms\Components\DatePicker::make('application_from')
                            ->label('Başvuru Tarihi (Başlangıç)'),
                        Forms\Components\DatePicker::make('application_until')
                            ->label('Başvuru Tarihi (Bitiş)'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['application_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('application_date', '>=', $date),
                            )
                            ->when(
                                $data['application_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('application_date', '<=', $date),
                            );
                    })
                    ->label('Başvuru Tarihi'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Yeni Başvuru'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Görüntüle'),
                Tables\Actions\EditAction::make()
                    ->label('Düzenle'),
                Tables\Actions\DeleteAction::make()
                    ->label('Sil'),
                Tables\Actions\Action::make('approve')
                    ->label('Onayla')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => in_array($record->status, [
                        'awaiting_evaluation',
                        'scholarship_pool',
                        'awaiting_documents',
                        'documents_under_review',
                        'interview_pool'
                    ]))
                    ->action(function ($record) {
                        $record->status = 'accepted';
                        $record->reviewed_at = now();
                        $record->reviewed_by = auth()->id();
                        $record->save();
                        
                        // Create a notification for the user about the approval
                        \App\Models\Notifications::create([
                            'notifiable_id' => $record->user_id,
                            'notifiable_type' => \App\Models\User::class,
                            'title' => 'Başvurunuz Kabul Edildi',
                            'message' => 'Başvurunuz değerlendirilmiş ve kabul edilmiştir.',
                            'type' => 'application_status',
                            'application_id' => $record->id,
                            'is_read' => false,
                        ]);
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Reddet')
                    ->color('danger')
                    ->icon('heroicon-o-x-mark')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Red Nedeni')
                            ->required(),
                    ])
                    ->visible(fn ($record) => in_array($record->status, [
                        'awaiting_evaluation',
                        'scholarship_pool',
                        'awaiting_documents',
                        'documents_under_review',
                        'interview_pool'
                    ]))
                    ->action(function ($record, array $data) {
                        $record->status = 'rejected';
                        $record->rejection_reason = $data['rejection_reason'];
                        $record->reviewed_at = now();
                        $record->reviewed_by = auth()->id();
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
                    }),
                Tables\Actions\Action::make('waitlist')
                    ->label('Yedek Listeye Al')
                    ->color('info')
                    ->icon('heroicon-o-clock')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => in_array($record->status, [
                        'awaiting_evaluation',
                        'scholarship_pool',
                        'awaiting_documents',
                        'documents_under_review',
                        'interview_pool'
                    ]))
                    ->action(function ($record) {
                        $record->status = 'interview_pool';
                        $record->reviewed_at = now();
                        $record->reviewed_by = auth()->id();
                        $record->save();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Toplu Sil'),
                    Tables\Actions\BulkAction::make('approve_selected')
                        ->label('Seçilenleri Onayla')
                        ->color('success')
                        ->icon('heroicon-o-check')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->status = 'accepted';
                                $record->reviewed_at = now();
                                $record->reviewed_by = auth()->id();
                                $record->save();
                                
                                // Create a notification for the user about the approval
                                \App\Models\Notifications::create([
                                    'notifiable_id' => $record->user_id,
                                    'notifiable_type' => \App\Models\User::class,
                                    'title' => 'Başvurunuz Kabul Edildi',
                                    'message' => 'Başvurunuz değerlendirilmiş ve kabul edilmiştir.',
                                    'type' => 'application_status',
                                    'application_id' => $record->id,
                                    'is_read' => false,
                                ]);
                            });
                        }),
                    Tables\Actions\BulkAction::make('reject_selected')
                        ->label('Seçilenleri Reddet')
                        ->color('danger')
                        ->icon('heroicon-o-x-mark')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\Textarea::make('rejection_reason')
                                ->label('Red Nedeni')
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            $records->each(function ($record) use ($data) {
                                $record->status = 'rejected';
                                $record->rejection_reason = $data['rejection_reason'];
                                $record->reviewed_at = now();
                                $record->reviewed_by = auth()->id();
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
                            });
                        }),
                ]),
            ]);
    }
}