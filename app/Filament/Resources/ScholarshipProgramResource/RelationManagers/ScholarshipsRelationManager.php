<?php

namespace App\Filament\Resources\ScholarshipProgramResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Scholarship;

class ScholarshipsRelationManager extends RelationManager
{
    protected static string $relationship = 'scholarships';

    protected static ?string $title = 'Burslar';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Öğrenci')
                    ->required()
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function (callable $set, $state) {
                        // When user is selected, find their latest valid application
                        if ($state) {
                            $application = \App\Models\Applications::where('user_id', $state)
                                ->whereIn('status', ['accepted', 'final_acceptance', 'interview_completed'])
                                ->latest()
                                ->first();
                            
                            if ($application) {
                                $set('application_id', $application->id);
                            }
                        }
                    }),
                Forms\Components\Select::make('application_id')
                    ->label('Başvuru')
                    ->options(function (callable $get) {
                        $userId = $get('user_id');
                        if (!$userId) {
                            return [];
                        }
                        
                        return \App\Models\Applications::where('user_id', $userId)
                            ->whereIn('status', ['accepted', 'final_acceptance', 'interview_completed'])
                            ->pluck('id', 'id')
                            ->toArray();
                    })
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('amount')
                    ->label('Tutar')
                    ->numeric()
                    ->prefix('₺')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Durum')
                    ->options([
                        'active' => 'Aktif',
                        'suspended' => 'Askıya Alındı',
                        'completed' => 'Tamamlandı',
                        'terminated' => 'Sonlandırıldı',
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('start_date')
                    ->label('Başlangıç Tarihi')
                    ->required(),
                Forms\Components\DatePicker::make('end_date')
                    ->label('Bitiş Tarihi')
                    ->required()
                    ->after('start_date'),
                Forms\Components\Textarea::make('notes')
                    ->label('Notlar')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Burs bulunamadı')
            ->emptyStateDescription('Bu programa ait burs bulunamadı. Yeni bir burs eklemek için "Yeni Burs" düğmesine tıklayın.')
           
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Öğrenci')
                    ->searchable(),
                Tables\Columns\TextColumn::make('application_id')
                    ->label('Başvuru No')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Tutar')
                    ->money('TRY')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'suspended' => 'warning',
                        'completed' => 'info',
                        'terminated' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'suspended' => 'Askıya Alındı',
                        'completed' => 'Tamamlandı',
                        'terminated' => 'Sonlandırıldı',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Başlangıç Tarihi')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Bitiş Tarihi')
                    ->date()
                    ->sortable(),
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
                        'active' => 'Aktif',
                        'suspended' => 'Askıya Alındı',
                        'completed' => 'Tamamlandı',
                        'terminated' => 'Sonlandırıldı',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Yeni Burs'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Görüntüle'),
                Tables\Actions\EditAction::make()
                    ->label('Düzenle'),
                Tables\Actions\DeleteAction::make()
                    ->label('Sil'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Toplu Sil'),
                ]),
            ]);
    }
} 