<?php

namespace App\Filament\Resources\User;

use App\Filament\Resources\User\ApplicantPoolResource\Pages;
use App\Filament\Resources\User\ApplicantPoolResource\RelationManagers;
use App\Models\Applications;
use App\Models\ScholarshipProgram;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class ApplicantPoolResource extends Resource
{
    protected static ?string $model = Applications::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationLabel = 'Başvuru Havuzu';
    
    protected static ?int $navigationSort = 1;
    
    protected static ?string $navigationGroup = 'Başvuru İşlemleri';

    protected static ?string $title = 'Başvuru Havuzu Yönetimi';

    protected static ?string $breadcrumb = 'Başvuru Havuzu';

    protected static ?string $breadcrumbParent = 'Başvuru Havuzu';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('status', 'dogrulama_tamamlandi')
            ->where('are_documents_approved', true)
            ->where(function ($query) {
                // Check if is_interview_completed is false or NULL
                $query->where('is_interview_completed', false)
                      ->orWhereNull('is_interview_completed');
            })
            ->orderBy('updated_at', 'desc');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Başvuru Bilgileri')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Başvuran')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('program_id')
                            ->label('Program')
                            ->relationship('program', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('application_number')
                            ->label('Başvuru Numarası'),
                        Forms\Components\DatePicker::make('application_date')
                            ->label('Başvuru Tarihi'),
                        Forms\Components\Select::make('status')
                            ->label('Durum')
                            ->options([
                                'beklemede' => 'Beklemede',
                                'belgeler_yuklendi' => 'Belgeler Yüklendi',
                                'dogrulama_bekliyor' => 'Doğrulama Bekliyor',
                                'dogrulama_tamamlandi' => 'Doğrulama Tamamlandı',
                                'mulakat_havuzu' => 'Mülakat Havuzu',
                                'mulakat_planlandi' => 'Mülakat Planlandı',
                                'mulakat_tamamlandi' => 'Mülakat Tamamlandı',
                                'kabul_edildi' => 'Kabul Edildi',
                                'reddedildi' => 'Reddedildi',
                            ]),
                        Forms\Components\Toggle::make('are_documents_approved')
                            ->label('Belgeler Doğrulandı'),
                        Forms\Components\Toggle::make('is_interview_scheduled')
                            ->label('Mülakat Planlandı'),
                        Forms\Components\Toggle::make('is_interview_completed')
                            ->label('Mülakat Tamamlandı'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Adayı Mülakata Davet Et')
                    ->schema([
                        Forms\Components\Select::make('interviewer_id')
                            ->label('Mülakatçı')
                            ->options(
                                User::query()
                                    ->where('is_admin', true)
                                    ->get()
                                    ->pluck('name', 'id')
                                    ->toArray()
                            )
                            ->searchable()
                            ->required(),
                        Forms\Components\DateTimePicker::make('interview_date')
                            ->label('Mülakat Tarihi ve Saati')
                            ->required()
                            ->minDate(now()),
                        Forms\Components\TextInput::make('location')
                            ->label('Konum')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('meeting_link')
                            ->label('Toplantı Linki')
                            ->url()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notlar')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Başvuru havuzunda başvuru bulunamadı')
            ->emptyStateDescription('Yeni bir başvuru oluşturmak için "Yeni Başvuru Havuzu" düğmesine tıklayın.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Yeni Başvuru Havuzu')
                    ->icon('heroicon-o-user-group'),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('application_number')
                    ->label('Başvuru No')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Başvuran')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('program.name')
                    ->label('Program')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('application_date')
                    ->label('Başvuru Tarihi')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'beklemede' => 'gray',
                        'belgeler_yuklendi' => 'info',
                        'dogrulama_bekliyor' => 'warning',
                        'dogrulama_tamamlandi' => 'success',
                        'mulakat_havuzu' => 'primary',
                        'mulakat_planlandi' => 'primary',
                        'mulakat_tamamlandi' => 'success',
                        'kabul_edildi' => 'success',
                        'reddedildi' => 'danger',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'beklemede' => 'Beklemede',
                        'belgeler_yuklendi' => 'Belgeler Yüklendi',
                        'dogrulama_bekliyor' => 'Doğrulama Bekliyor',
                        'dogrulama_tamamlandi' => 'Doğrulama Tamamlandı',
                        'mulakat_havuzu' => 'Mülakat Havuzu',
                        'mulakat_planlandi' => 'Mülakat Planlandı',
                        'mulakat_tamamlandi' => 'Mülakat Tamamlandı',
                        'kabul_edildi' => 'Kabul Edildi',
                        'reddedildi' => 'Reddedildi',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\IconColumn::make('are_documents_approved')
                    ->label('Belgeler Doğrulandı')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_interview_completed')
                    ->label('Mülakat Tamamlandı')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Son Güncelleme')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('program')
                    ->label('Program')
                    ->options(ScholarshipProgram::all()->pluck('name', 'id')),
                Tables\Filters\Filter::make('application_date')
                    ->label('Başvuru Tarihi')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Başlangıç'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Bitiş'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->where('application_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->where('application_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Görüntüle'),
                Tables\Actions\EditAction::make()
                    ->label('Mülakata Davet Et'),
                Tables\Actions\Action::make('send_to_pool')
                    ->label('Havuza Ekle')
                    ->icon('heroicon-o-user-group')
                    ->color('success')
                    ->action(function (Applications $record) {
                        $record->status = 'mulakat_havuzu';
                        $record->save();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Başvuru havuza eklensin mi?')
                    ->modalDescription('Bu adayı mülakat havuzuna eklemek istediğinizden emin misiniz?')
                    ->modalSubmitActionLabel('Evet, Havuza Ekle'),
                Tables\Actions\Action::make('schedule_interview')
                    ->label('Mülakat Planla')
                    ->icon('heroicon-o-calendar')
                    ->color('primary')
                    ->form([
                        Forms\Components\Select::make('interviewer_id')
                            ->label('Mülakatçı')
                            ->options(
                                User::query()
                                    ->where('is_admin', true)
                                    ->get()
                                    ->pluck('name', 'id')
                                    ->toArray()
                            )
                            ->searchable()
                            ->required(),
                        Forms\Components\DateTimePicker::make('interview_date')
                            ->label('Mülakat Tarihi ve Saati')
                            ->required()
                            ->minDate(now()),
                        Forms\Components\TextInput::make('location')
                            ->label('Konum')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('meeting_link')
                            ->label('Toplantı Linki')
                            ->url()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notlar')
                            ->maxLength(65535),
                    ])
                    ->action(function (Applications $record, array $data) {
                        // Başvuru durumunu güncelle
                        $record->status = 'mulakat_planlandi';
                        $record->save();
                        
                        // Mülakat kaydı oluştur
                        $interview = new \App\Models\Interviews();
                        $interview->application_id = $record->id;
                        $interview->interviewer_admin_id = $data['interviewer_id'];
                        $interview->scheduled_date = $data['interview_date'];
                        $interview->status = 'scheduled';
                        
                        if (isset($data['location'])) {
                            $interview->location = $data['location'];
                        }
                        
                        if (isset($data['meeting_link'])) {
                            $interview->meeting_link = $data['meeting_link'];
                        }
                        
                        if (isset($data['notes'])) {
                            $interview->notes = $data['notes'];
                        }
                        
                        $interview->save();
                    }),
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
                        $record->status = 'reddedildi';
                        $record->rejection_reason = $data['rejection_reason'];
                        $record->rejection_date = Carbon::now();
                        $record->save();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Başvuru reddedilsin mi?')
                    ->modalDescription('Bu başvuruyu reddetmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')
                    ->modalSubmitActionLabel('Evet, Reddet'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_add_to_pool')
                        ->label('Havuza Ekle')
                        ->icon('heroicon-o-user-group')
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                $record->status = 'mulakat_havuzu';
                                $record->save();
                            }
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Başvurular havuza eklensin mi?')
                        ->modalDescription('Seçili başvuruları mülakat havuzuna eklemek istediğinizden emin misiniz?')
                        ->modalSubmitActionLabel('Evet, Havuza Ekle'),
                    Tables\Actions\BulkAction::make('bulk_reject')
                        ->label('Reddet')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->form([
                            Forms\Components\Textarea::make('rejection_reason')
                                ->label('Ret Sebebi')
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            foreach ($records as $record) {
                                $record->status = 'reddedildi';
                                $record->rejection_reason = $data['rejection_reason'];
                                $record->rejection_date = Carbon::now();
                                $record->save();
                            }
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Başvurular reddedilsin mi?')
                        ->modalDescription('Seçili başvuruları reddetmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')
                        ->modalSubmitActionLabel('Evet, Reddet'),
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Sil'),
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
            'index' => Pages\ListApplicantPools::route('/'),
            'create' => Pages\CreateApplicantPool::route('/create'),
            'view' => Pages\ViewApplicantPool::route('/{record}'),
            'edit' => Pages\EditApplicantPool::route('/{record}/edit'),
        ];
    }
}
