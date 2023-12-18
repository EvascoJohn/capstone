<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReposessionResource\Pages;
use App\Filament\Resources\ReposessionResource\RelationManagers;
use App\Models\CustomerApplication;
use App\Models\Reposession;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make("id")
                    ->wrap()
                    ->label("ID"),
                Tables\Columns\TextColumn::make("applicant_fullname")
                    ->wrap()
                    ->label("Owner"),
                Tables\Columns\TextColumn::make("unitModel.model_name")
                    ->wrap()
                    ->label("Model"),
                Tables\Columns\TextColumn::make("customerPaymentAccount.remaining_balance")
                    ->wrap()
                    ->label("Remaining Balance"),
                
            ])
            ->filters([
                // no filters required.
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
