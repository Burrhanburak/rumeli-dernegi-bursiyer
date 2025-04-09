<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ScholarshipsRelationManager extends RelationManager
{
    protected static string $relationship = 'scholarships';

    protected static ?string $title = 'Burslar';
    protected static ?string $breadcrumb = 'Burslar';
    protected static ?string $breadcrumbParent = 'Kullanıcılar';

    protected static ?string $createButtonLabel = 'Yeni Burs';

    protected static ?string $createButtonIcon = 'heroicon-o-plus';

    protected static ?string $createButtonColor = 'success';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                ->label('Program Adı')
                ->required()
                ->maxLength(255),
            Forms\Components\RichEditor::make('description')
                ->label('Açıklama')
                ->columnSpanFull(),
            Forms\Components\TextInput::make('default_amount')
                ->label('Burs Tutarı')
                ->numeric()
                ->prefix('₺')
                ->required(),
                Forms\Components\Select::make('status')
                    ->label('Burs Durumu')
                    ->options([
                        'active' => 'Active',
                        'suspended' => 'Suspended',
                        'completed' => 'Completed',
                        'terminated' => 'Terminated',
                    ])
                    ->required()
                    ->default('active'),
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
        ->emptyStateHeading('Burs Programları bulunamadı')
        ->emptyStateDescription('Yeni bir burs programı oluşturmak için "Yeni Program" düğmesine tıklayın.')
        ->emptyStateActions([
            Tables\Actions\CreateAction::make()
                ->label('Burs Programı')
                ->icon('heroicon-o-academic-cap'),
        ])
        ->columns([
            Tables\Columns\TextColumn::make('name')
                ->label('Program Adı')
                ->searchable(),
            Tables\Columns\TextColumn::make('amount')
                ->label('Tutar')
                ->money('try')
                ->sortable(),
            Tables\Columns\TextColumn::make('max_recipients')
            // ->relationship('program', 'max_recipients')
                ->label('Kontenjan')
                ->numeric()
                ->sortable(),
            Tables\Columns\IconColumn::make('status')
                ->label('Durum')
                ->boolean(),
            Tables\Columns\TextColumn::make('start_date')
                ->label('Başvuru Başlangıcı')
                ->date()
                ->sortable(),
            Tables\Columns\TextColumn::make('end_date')
                ->label('Başvuru Bitişi')
                ->date()
                ->sortable(),
                Tables\Columns\TextColumn::make('program_start_date')
                ->label('Program Başlangıcı')
                ->date()
                ->sortable(),
                Tables\Columns\TextColumn::make('program_end_date')
                ->label('Program Bitişi')
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
            Tables\Filters\Filter::make('is_active')
                ->label('Aktif Programlar')
                ->query(fn (Builder $query): Builder => $query->where('is_active', true))
                ->toggle(),
            Tables\Filters\Filter::make('current')
                ->label('Güncel Programlar')
                ->query(fn (Builder $query): Builder => $query->where('program_start_date', '<=', now())->where('program_end_date', '>=', now()))
                ->toggle(),
            Tables\Filters\Filter::make('accepting_applications')
                ->label('Başvuruya Açık')
                ->query(fn (Builder $query): Builder => $query->where('application_end_date', '>=', now()))
                ->toggle(),
        ])
        ->actions([
            Tables\Actions\ViewAction::make()
                ->label('Görüntüle'),
            Tables\Actions\EditAction::make()
                ->label('Düzenle')
               ,
            Tables\Actions\DeleteAction::make()
                ->label('Sil')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Burs Programını Sil')
                ->modalDescription('Bu burs programını silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')
                ->modalSubmitActionLabel('Evet, Sil')
                ->modalCancelActionLabel('İptal'),
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
                }),
            ]),
        ]);
    }
} 