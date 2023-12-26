<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerPaymentAccountResource\Pages;
use App\Models;
use App\Filament\Resources\CustomerPaymentAccountResource\RelationManagers;
use App\Models\CustomerApplication;
use App\Models\CustomerPaymentAccount;
use App\Models\UnitModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerPaymentAccountResource extends Resource
{
    protected static ?string $model = CustomerPaymentAccount::class;

    // protected static ?string $navigationLabel = 'Installment';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationGroup = 'Payments';

    protected static ?string $modelLabel = "Payment Management";

    protected static ?string $pluralModelLabel = 'Installments';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make("customer_application_id")
                    ->searchable()
                    ->columnSpan(1)
                    ->getSearchResultsUsing(fn(string $search): array => Models\CustomerApplication::searchApprovedApplicationsWithNoAccounts($search)
                            ->get()->pluck("applicant_full_name", "id")->toArray())
                    ->getOptionLabelUsing(fn($value): ?string => Models\CustomerApplication::find($value)->id)
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (string $state, Forms\Set $set, ?Model $record) {
                        $cust_app = Models\CustomerApplication::where("id", $state)->first();
                        $payment_status = "downpayment";
                        if ($cust_app->plan_type == "Cash") {
                            $payment_status = "cash payment";
                        } else if ($cust_app->plan_type == "Installment") {
                            $payment_status = "down payment";
                        }
                        $remaining = $cust_app->unitModel->price;
                        if ($state != "" || $state != null) {
                            $set('remaining_balance', $remaining);
                            $set('plan_type', $cust_app->plan);
                            // monthly interest.
                            $set('monthly_payment', $cust_app->unit_monthly_amort_fin);
                            $set('down_payment', $cust_app->unit_ttl_dp);
                            $set('term', $cust_app->unit_term);
                            $set('status', $cust_app->application_status);
                            $set('payment_status', $payment_status);
                            $set('original_amount', $cust_app->unitModel->price);
                        }
                    }),
                Forms\Components\TextInput::make("monthly_interest")
                    ->minValue(0)
                    ->numeric()
                    ->maxValue(100)
                    ->required(),
                Forms\Components\TextInput::make("remaining_balance")
                    ->readOnly(),
                Forms\Components\TextInput::make("plan_type")
                    ->readOnly(),
                Forms\Components\TextInput::make("monthly_payment")
                    ->readOnly(),
                Forms\Components\TextInput::make("down_payment")
                    ->minValue(0)
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make("term")
                    ->readOnly(),
                Forms\Components\TextInput::make("status")
                    ->readOnly(),
                Forms\Components\TextInput::make("payment_status")
                    ->readOnly(),
                Forms\Components\TextInput::make("original_amount")
                    ->readOnly(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make("id"),
                Tables\Columns\TextColumn::make("customer_application_id")
                        ->label("Application ID"),
                Tables\Columns\TextColumn::make("customerApplication.applicant_full_name")
                        ->label('Applicant Name'),
                Tables\Columns\TextColumn::make("original_amount")
                        ->money("PHP"),
                Tables\Columns\TextColumn::make("remaining_balance")
                        ->money("PHP"),
                Tables\Columns\TextColumn::make("payment_status")
                        ->badge(),
                Tables\Columns\TextColumn::make("term")
                        ->badge(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // no bulk actions.
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
            'index' => Pages\ListCustomerPaymentAccounts::route('/'),
            'create' => Pages\CreateCustomerPaymentAccount::route('/create'),
            // 'edit' => Pages\EditCustomerPaymentAccount::route('/{record}/edit'),
        ];
    }
}
