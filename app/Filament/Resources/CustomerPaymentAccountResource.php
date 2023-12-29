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

    protected static ?string $navigationLabel = 'Payments';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = "Payments";

    protected static ?string $pluralModelLabel = 'Payments';


    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\TextInput::make("remaining_balance")
                    ->readOnly(),
                Forms\Components\TextInput::make("plan_type")
                    ->readOnly(),
                Forms\Components\TextInput::make("monthly_payment")
                    ->readOnly(),
                Forms\Components\TextInput::make("term")
                    ->readOnly(),
                Forms\Components\TextInput::make("term_left")
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
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('make payment'),
            ])
            ->bulkActions([
                // no bulk actions.
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomerPaymentAccounts::route('/'),
            'create' => Pages\CreateCustomerPaymentAccount::route('/create'),
            'edit' => Pages\EditCustomerPaymentAccount::route('{record}/edit'),
            'view' => Pages\ViewCustomerPaymentAccount::route('/{record}'),
        ];
    }
}
