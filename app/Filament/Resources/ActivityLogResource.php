<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use App\Filament\Resources\ActivityLogResource\RelationManagers;
use App\Models\ActivityLog;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Utilities';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('log_name')
                    ->label('Model')
                    ->columnSpanFull()
                    ->maxLength(255),
                Forms\Components\TextInput::make('subject_type')
                    ->maxLength(255),
                Forms\Components\TextInput::make('event')
                    ->maxLength(255),
                Forms\Components\TextInput::make('subject_id')
                    ->numeric(),
                Forms\Components\TextInput::make('causer_type')
                    ->maxLength(255),
                Forms\Components\TextInput::make('causer_id')
                    ->numeric(),
                // Forms\Components\Textarea::make('description')
                //     ->required()
                //     ->maxLength(65535)
                //     ->columnSpanFull(),
                // Forms\Components\TextInput::make('properties'),
                // Forms\Components\TextInput::make('batch_uuid')
                //     ->maxLength(36),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.firstname'),
                Tables\Columns\TextColumn::make('log_name')
                    ->label('Model'),
                Tables\Columns\TextColumn::make('event')
                    ->badge(),
                Tables\Columns\TextColumn::make('subject_type')
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('subject_id'),
                Tables\Columns\TextColumn::make('causer_type')
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('batch_uuid')
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->toggledHiddenByDefault(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->color('primary'),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListActivityLogs::route('/'),
            'create' => Pages\CreateActivityLog::route('/create'),
            'edit' => Pages\EditActivityLog::route('/{record}/edit'),
        ];
    }
}
