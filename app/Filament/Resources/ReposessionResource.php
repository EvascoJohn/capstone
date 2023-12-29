<?php

namespace App\Filament\Resources;

use App\Enums\ApplicationStatus;
use App\Filament\Resources\ReposessionResource\Pages;
use App\Filament\Resources\ReposessionResource\RelationManagers;
use App\Models\CustomerApplication;
use App\Models\Reposession;
use App\Models;
use App\Models\CustomerPaymentAccount;
use Carbon\Carbon;
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
    protected static ?string $model = CustomerPaymentAccount::class;

    protected static ?string $navigationLabel = 'Repossession';
    protected static ?string $modelLabel = "Repossession";
    protected static ?string $navigationIcon = 'heroicon-o-hand-thumb-up';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // forms for passing the unit and payments to the new application.
                Forms\Components\TextInput::make("id")
                    ->columnSpan(1)
                    ->label("Account ID")
                    ->hint("account that is being repossesed")
                    ->readOnly(),
                Forms\Components\Select::make("assumed_by_id")
                    ->searchable()
                    ->columnSpan(1)
                    ->getSearchResultsUsing(fn(string $search): array => Models\CustomerApplication::searchApprovedApplicationsWithNoAccountsPrefersRepo($search)
                        ->get()->pluck("applicant_full_name", "id")->toArray())
                    ->getOptionLabelUsing(fn($value): ?string => Models\CustomerApplication::find($value)->id)
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (string $state, Forms\Set $set, ?Model $record) {
                        $cust_app = Models\CustomerApplication::where("id", $state)->first();
                        if ($state != "" || $state != null) {
                            $set('assumed_by_full_name', $cust_app->applicant_full_name);
                            $set('preferred_unit_status', $cust_app->preffered_unit_status);
                            $set('created_at', $cust_app->created_at);
                        }
                    }),
                Forms\Components\Textarea::make('reposession_note')
                    ->label('Note'),
                Forms\Components\TextInput::make('assumed_by_full_name')
                    ->label('Full name'),
                Forms\Components\TextInput::make('preferred_unit_status')
                    ->label('Preferred Unit Status'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make("id")
                    ->wrap()
                    ->label("ID"),
                Tables\Columns\TextColumn::make("customerApplication.applicant_full_name")
                    ->wrap()
                    ->label("Owner"),
                Tables\Columns\TextColumn::make("due_date")
                    ->wrap()
                    ->label("Application Status"),
                Tables\Columns\TextColumn::make("customerApplication.unitModel.model_name")
                    ->wrap()
                    ->label("Model"),
                Tables\Columns\TextColumn::make("remaining_balance")
                    ->money("PHP")
                    ->label("Remaining Balance"),

            ])
            ->filters([
                // no filters required.
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('reposess'),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->wherehas('payments', function (Builder $query){
                $twoMonthsAgo = Carbon::now()->subMonths(2);
                $query->where('created_at', '<', $twoMonthsAgo);
            });
    }
}
