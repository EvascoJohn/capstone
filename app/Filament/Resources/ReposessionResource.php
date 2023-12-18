<?php

namespace App\Filament\Resources;

use App\Enums\ApplicationStatus;
use App\Filament\Resources\ReposessionResource\Pages;
use App\Filament\Resources\ReposessionResource\RelationManagers;
use App\Models\CustomerApplication;
use App\Models\Reposession;
use App\Models;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReposessionResource extends Resource
{
    protected static ?string $model = CustomerApplication::class;

    protected static ?string $navigationLabel = 'Reposession';
    protected static ?string $modelLabel = "Reposession";
    protected static ?string $navigationGroup = 'Reposession';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // forms for passing the unit and payments to the new application.
                Forms\Components\Textarea::make('reposession_note')
                        ->label('Note'),
                Forms\Components\TextInput::make('assumed_by_firstname')
                        ->label('First name'),
                Forms\Components\TextInput::make('assumed_by_middlename')
                        ->label('Middle name'),
                Forms\Components\TextInput::make('assumed_by_lastname')
                        ->label('Last name'),
                Forms\Components\Select::make('assumed_by_id')
                        ->required()
                        ->live()
                        ->label("Assumed By")
                        ->options(
                                fn (?Model $record): array => $record::where('application_status', ApplicationStatus::APPROVED_STATUS->value)
                                        ->limit(20)
                                        ->pluck('id', 'id')
                                        ->toArray()
                        )
                        ->afterStateUpdated(
                                function(Forms\Get $get, Forms\Set $set)
                                {
                                    if($get('assumed_by_id') != ""){
                                        $set('assumed_by_firstname', Models\CustomerApplication::where('id', $get('assumed_by_id'))->first()->applicant_firstname);
                                        $set('assumed_by_middlename', Models\CustomerApplication::where('id', $get('assumed_by_id'))->first()->applicant_middlename);
                                        $set('assumed_by_lastname', Models\CustomerApplication::where('id', $get('assumed_by_id'))->first()->applicant_lastname);
                                    }
                                    else if($get('assumed_by_id') == "")
                                    {
                                        $set('assumed_by_firstname', "");
                                        $set('assumed_by_middlename', "");
                                        $set('assumed_by_lastname', "");
                                    }
                                }
                            ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make("id")
                    ->wrap()
                    ->label("ID"),
                Tables\Columns\TextColumn::make("applicant_full_name")
                    ->wrap()
                    ->label("Owner"),
                Tables\Columns\TextColumn::make("application_status")
                    ->wrap()
                    ->label("Application Status"),
                Tables\Columns\TextColumn::make("unitModel.model_name")
                    ->wrap()
                    ->label("Model"),
                Tables\Columns\TextColumn::make("customerPaymentAccount.remaining_balance")
                    ->money("PHP")
                    ->label("Remaining Balance"),
                
            ])
            ->filters([
                // no filters required.
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->label('reposession'),
            ])
            ->bulkActions([
                // no bulk actions required.
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
            'index' => Pages\ListReposessions::route('/'),
            // 'create' => Pages\CreateReposession::route('/create'), CANNOT CREATE A REPO, IT TAKES FROM THE CUSTOMER APPLICATION.
            'edit' => Pages\EditReposession::route('/{record}/edit'),
        ];
    }
}
