<?php

namespace App\Filament\Resources\User;

use App\Filament\Resources\User\AcceptedApplicationsResource\Pages;
use App\Filament\Resources\User\AcceptedApplicationsResource\RelationManagers;
use App\Models\Applications;
use App\Models\ScholarshipProgram;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;

class AcceptedApplicationsResource extends Resource
{
    protected static ?string $model = Applications::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-circle';
    
    protected static ?string $navigationLabel = 'Kabul Edilen Başvurular';
    
    protected static ?int $navigationSort = 2;
    
    protected static ?string $navigationGroup = 'Başvuru Yönetimi';

    protected static ?string $title = 'Kabul Edilen Başvurular';

    protected static ?string $breadcrumb = 'Kabul Edilen Başvurular';

    protected static ?string $breadcrumbParent = 'Başvuru Yönetimi';
    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('status', ['accepted', 'on_kabul', 'pre_approved', 'dogrulama_tamamlandi'])
            ->orderBy('updated_at', 'desc');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Başvuru Bilgileri')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required()
                            ->label('Başvuran')
                            ->disabled(),
                        Forms\Components\Select::make('program_id')
                            ->relationship('program', 'name')
                            ->searchable()
                            ->required()
                            ->label('Program')
                            ->disabled(),
                        Forms\Components\TextInput::make('application_id')
                            ->label('Başvuru ID')
                            ->disabled(),
                        Forms\Components\DatePicker::make('application_date')
                            ->label('Başvuru Tarihi')
                            ->disabled(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'accepted' => 'Kabul Edildi',
                                'on_kabul' => 'Ön Kabul',
                                'pre_approved' => 'Ön Kabul',
                                'dogrulama_tamamlandi' => 'Evrak Doğrulaması Tamamlandı',
                            ])
                            ->label('Durum')
                            ->disabled(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notlar')
                            ->columnSpanFull()
                            ->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Kabul edilmiş başvuru bulunamadı')
            ->emptyStateDescription('Başvuru ön değerlendirmesinde kabul edilmiş başvurular burada görüntülenecektir.')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('application_id')
                    ->label('Başvuru ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Başvuran')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('program.name')
                    ->label('Program')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'accepted' => 'success',
                        'on_kabul' => 'success',
                        'pre_approved' => 'success',
                        'dogrulama_tamamlandi' => 'success',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'accepted' => 'Kabul Edildi',
                        'on_kabul' => 'Ön Kabul',
                        'pre_approved' => 'Ön Kabul',
                        'dogrulama_tamamlandi' => 'Evrak Doğrulaması Tamamlandı',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\IconColumn::make('are_documents_approved')
                    ->label('Belgeler Onaylandı')
                    ->boolean()
                    ->trueIcon('heroicon-o-document-check')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_interview_completed')
                    ->label('Mülakat Tamamlandı')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->sortable(),
                Tables\Columns\TextColumn::make('application_date')
                    ->date()
                    ->sortable()
                    ->label('Başvuru Tarihi'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Son Güncelleme'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'accepted' => 'Kabul Edildi',
                        'on_kabul' => 'Ön Kabul',
                        'pre_approved' => 'Ön Kabul',
                        'dogrulama_tamamlandi' => 'Evrak Doğrulaması Tamamlandı',
                    ])
                    ->label('Durum'),
                Tables\Filters\SelectFilter::make('program')
                    ->relationship('program', 'name')
                    ->label('Program'),
                Tables\Filters\Filter::make('documents_approved')
                    ->label('Belgeleri Onaylanmış')
                    ->query(fn (Builder $query): Builder => $query->where('are_documents_approved', true))
                    ->toggle(),
                Tables\Filters\Filter::make('interview_completed')
                    ->label('Mülakatı Tamamlanmış')
                    ->query(fn (Builder $query): Builder => $query->where('is_interview_completed', true))
                    ->toggle(),
                Tables\Filters\Filter::make('ready_for_scholarship')
                    ->label('Bursa Hazır')
                    ->query(fn (Builder $query): Builder => $query
                        ->where('are_documents_approved', true)
                        ->where('is_interview_completed', true))
                    ->toggle(),
                Tables\Filters\Filter::make('application_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Başlangıç'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Bitiş'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->where('application_date', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->where('application_date', '<=', $date),
                            );
                    })
                    ->label('Başvuru Tarihi'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Görüntüle'),
                Tables\Actions\Action::make('verify_documents')
                    ->label('Belgeleri Onayla')
                    ->icon('heroicon-o-document-check')
                    ->color('success')
                    ->visible(fn (Applications $record): bool => !$record->are_documents_approved)
                    ->action(function (Applications $record) {
                        $record->are_documents_approved = true;
                        $record->document_reviewed_by = auth()->id();
                        $record->document_reviewed_at = now();
                        $record->save();
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Belgeler Onaylandı')
                            ->body('Başvuru belgeleri başarıyla onaylandı.')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Belgeler onaylansın mı?')
                    ->modalDescription('Bu başvurunun belgelerini onaylamak istediğinizden emin misiniz?')
                    ->modalSubmitActionLabel('Evet, Onayla'),
                Tables\Actions\Action::make('schedule_interview')
                    ->label('Mülakat Planla')
                    ->icon('heroicon-o-calendar')
                    ->color('primary')
                    ->visible(fn (Applications $record): bool => 
                        $record->are_documents_approved && !in_array($record->status, ['interview_scheduled', 'interview_completed']))
                    ->form([
                        Forms\Components\DateTimePicker::make('scheduled_date')
                            ->label('Mülakat Tarihi ve Saati')
                            ->required()
                            ->minDate(now()),
                        Forms\Components\Select::make('interviewer_id')
                            ->label('Mülakatçı')
                            ->options(
                                \App\Models\User::query()
                                    ->where('is_admin', true)
                                    ->get()
                                    ->pluck('name', 'id')
                                    ->toArray()
                            )
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('location')
                            ->label('Konum')
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_online')
                            ->label('Online Mülakat mı?')
                            ->default(false),
                        Forms\Components\TextInput::make('meeting_link')
                            ->label('Toplantı Linki')
                            ->url()
                            ->maxLength(255)
                            ->visible(fn (Forms\Get $get) => $get('is_online')),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notlar')
                            ->maxLength(65535),
                    ])
                    ->action(function (Applications $record, array $data) {
                        // Find existing interview or create new one
                        $interview = \App\Models\Interviews::where('application_id', $record->id)->first();
                        
                        if (!$interview) {
                            // If no interview record exists, create a new one
                            $interview = \App\Models\Interviews::create([
                                'application_id' => $record->id,
                                'user_id' => $record->user_id,
                                'status' => 'awaiting_schedule',
                                'interview_date' => now(),
                            ]);
                        }
                        
                        // Update interview details
                        $interview->interviewer_admin_id = $data['interviewer_id'];
                        $interview->interview_date = $data['scheduled_date'];
                        $interview->scheduled_date = $data['scheduled_date'];
                        $interview->location = $data['location'] ?? null;
                        $interview->is_online = $data['is_online'] ?? false;
                        $interview->meeting_link = $data['meeting_link'] ?? null;
                        $interview->notes = $data['notes'] ?? null;
                        $interview->status = 'scheduled';
                        $interview->save();
                        
                        // Update application status
                        $record->status = 'interview_scheduled';
                        // Update the interview tracking in application
                        $record->interview_pool_at = now();
                        $record->interview_pool_by = auth()->id();
                        $record->save();
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Mülakat başarıyla planlandı')
                            ->success()
                            ->send();
                    })
                    ->modalHeading('Mülakat Planla')
                    ->modalDescription('Lütfen mülakat detaylarını girin')
                    ->modalSubmitActionLabel('Planla'),
                Tables\Actions\Action::make('transfer_scholarship')
                    ->label('Bursa Aktar')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('success')
                    ->visible(fn (Applications $record): bool => 
                        $record->are_documents_approved && $record->is_interview_completed)
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
                    ->button(),
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
            'index' => Pages\ListAcceptedApplications::route('/'),
            'create' => Pages\CreateAcceptedApplications::route('/create'),
            'edit' => Pages\EditAcceptedApplications::route('/{record}/edit'),
        ];
    }    
}
