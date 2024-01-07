<?php

namespace App\Filament\Resources;

use App\Enums\ApplicationStatus;
use App\Enums\UnitStatus;
use App\Filament\Resources\ReposessionResource\Pages;
use App\Filament\Resources\ReposessionResource\RelationManagers;
use App\Models\CustomerApplication;
use App\Models\Reposession;
use App\Models;
use App\Models\CustomerPaymentAccount;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
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
                Group::make()->schema([
                    Section::make()->schema([
                        Fieldset::make('Repossession Details')->schema([
                            Forms\Components\TextInput::make("id")
                                ->columnSpan(1)
                                ->label("Account ID")
                                ->hint("account that is being repossesed")
                                ->readOnly(),
                            Forms\Components\Hidden::make("customer_application_id"),
                            Forms\Components\Select::make("assumed_by_id")
                                ->searchable()
                                ->columnSpan(1)
                                ->options(
                                    function (Forms\Get $get) {
                                        return CustomerApplication::where('application_status', ApplicationStatus::APPROVED_STATUS)
                                            ->where('preffered_unit_status', UnitStatus::REPOSSESSION)
                                            ->where('id', '!=', $get('customer_application_id'))
                                            ->pluck('applicant_full_name', 'id');
                                    }
                                )
                                ->required()
                                ->live()
                                ->afterStateUpdated(function (string $state, Forms\Set $set, ?Model $record) {
                                    $cust_app = Models\CustomerApplication::where("id", $state)->first();
                                    if ($state != "" || $state != null) {
                                        $set('preferred_unit_status', $cust_app->preffered_unit_status);
                                        $set('application_status', $cust_app->application_status);
                                        $set('plan', $cust_app->plan);
                                    }
                                }),
                            Forms\Components\Textarea::make('note')
                                ->columnSpanFull()
                                ->rows(5)
                                ->label('Note'),
                        ])->columns(1)
                    ])
                ])->columnSpan(2),

                Group::make()->schema([
                    Section::make()->schema([
                        Fieldset::make('Account Details')->schema([
                            // Forms\Components\TextInput::make('assumed_by_full_name')
                            //     ->readOnly()
                            //     ->label('Full name'),
                            Forms\Components\TextInput::make('application_status')
                                ->readOnly(),
                            Forms\Components\TextInput::make('preferred_unit_status')
                                ->readOnly(),
                            Forms\Components\TextInput::make('plan')
                                ->readOnly(),
                        ])->columns(1)
                    ])
                ])->columnSpan(1)

            ])->columns(3);
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
                // Tables\Columns\TextColumn::make("due_date")
                //     ->wrap()
                //     ->label("Application Status"),
                Tables\Columns\TextColumn::make("customerApplication.unitModel.model_name")
                    ->wrap()
                    ->label("Model"),
                Tables\Columns\TextColumn::make("remaining_balance")
                    ->money("PHP")
                    ->label("Remaining Balance"),

            ])
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
                Tables\Actions\EditAction::make()
                    ->label('Repossess'),
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
            ->wherehas('payments', function (Builder $query) {
                $twoMonthsAgo = Carbon::now()->subMonths(2);
                $query->where('created_at', '<', $twoMonthsAgo);
            });
    }
}
