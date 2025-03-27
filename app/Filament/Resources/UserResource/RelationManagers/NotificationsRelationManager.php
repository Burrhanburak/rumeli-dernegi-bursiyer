<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;

class NotificationsRelationManager extends RelationManager
{
    protected static string $relationship = 'notifications';
    protected static ?string $title = 'Bildirimler';
    protected static ?string $breadcrumb = 'Bildirimler';
    protected static ?string $breadcrumbParent = 'Kullanıcılar';
    
    // Override default method for polymorph relationship support
    protected function getTableQuery(): Builder
    {
        return $this->getRelationship()->getQuery();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('message')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Select::make('type')
                    ->options([
                        'document_required' => 'Document Required',
                        'document_approved' => 'Document Approved',
                        'document_rejected' => 'Document Rejected',
                        'interview_scheduled' => 'Interview Scheduled',
                        'interview_reminder' => 'Interview Reminder',
                        'application_status' => 'Application Status',
                        'scholarship_awarded' => 'Scholarship Awarded',
                        'scholarship_changed' => 'Scholarship Changed',
                        'system' => 'System',
                    ])
                    ->required(),
                Forms\Components\Toggle::make('is_read')
                    ->required()
                    ->default(false),
                Forms\Components\DateTimePicker::make('read_at'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Bildirim bulunamadı')
            ->emptyStateDescription('Yeni bir bildirim eklemek için "Yeni Bildirim" düğmesine tıklayın.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Yeni Bildirim')
            ])
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_read')
                    ->boolean()
                    ->label('Read')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Sent At'),
                Tables\Columns\TextColumn::make('read_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_read')
                    ->options([
                        true => 'Read',
                        false => 'Unread',
                    ]),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'document_required' => 'Document Required',
                        'document_approved' => 'Document Approved',
                        'document_rejected' => 'Document Rejected',
                        'interview_scheduled' => 'Interview Scheduled',
                        'interview_reminder' => 'Interview Reminder',
                        'application_status' => 'Application Status',
                        'scholarship_awarded' => 'Scholarship Awarded',
                        'scholarship_changed' => 'Scholarship Changed',
                        'system' => 'System',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->using(function (array $data, string $model): Model {
                        $resourceRecord = $this->getOwnerRecord();
                        
                        return $resourceRecord->notifications()->create([
                            ...$data,
                            'notifiable_id' => $resourceRecord->getKey(),
                            'notifiable_type' => get_class($resourceRecord),
                        ]);
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
} 