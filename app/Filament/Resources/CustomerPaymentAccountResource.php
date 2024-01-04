<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerPaymentAccountResource\Pages;
use App\Models;
use App\Filament\Resources\CustomerPaymentAccountResource\RelationManagers;
use App\Models\CustomerApplication;
use App\Models\CustomerPaymentAccount;
use App\Models\UnitModel;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class CustomerPaymentAccountResource extends Resource
{
    protected static ?string $model = CustomerPaymentAccount::class;

    protected static ?string $navigationLabel = 'Payments';

    // protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'Payment';

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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
        ->columns(4)
        ->schema([
                InfoLists\Components\Tabs::make("")
                        ->columns(6)
                        ->columnSpan(6)
                        ->tabs([
                                InfoLists\Components\Tabs\Tab::make("Account Information")
                                        ->columns(6)
                                        ->schema([
                                                InfoLists\Components\Section::make("Customer's Information")
                                                        ->aside()
                                                        ->columns(12)
                                                        ->description("Information about the customer's account for payment")
                                                        ->schema([
                                                                InfoLists\Components\TextEntry::make('customerApplication.applicant_full_name')
                                                                        ->columnSpan(4)
                                                                        ->label("Full Name"),
                                                                InfoLists\Components\TextEntry::make('created_at')
                                                                        ->columnSpan(4)
                                                                        ->dateTime('M d Y')
                                                                        ->label('Date Created')
                                                                        ->badge(),
                                                                InfoLists\Components\TextEntry::make('due_date')
                                                                        ->date()
                                                                        ->badge()
                                                                        ->hidden(function(?string $state){
                                                                                if($state != null){
                                                                                    return false;
                                                                                }
                                                                                return true;
                                                                        })
                                                                        ->columnSpan(4)
                                                                        ->label('Upcoming Due')
                                                                        ->color('danger'),
                                                    ]),
                                                InfoLists\Components\Section::make("Account's Details")
                                                        ->columns(12)
                                                        ->description("Details of the customer's account")
                                                        ->schema([
                                                                InfoLists\Components\TextEntry::make('customer_application_id')
                                                                        ->columnSpan(2)
                                                                        ->label("Application ID")
                                                                        ->badge(),
                                                                InfoLists\Components\TextEntry::make('plan_type')
                                                                        ->columnSpan(2)
                                                                        ->label("Plan")
                                                                        ->badge(),
                                                                InfoLists\Components\TextEntry::make('term')
                                                                        ->columnSpan(2)
                                                                        ->label("Term")
                                                                        ->badge(),
                                                                InfoLists\Components\TextEntry::make('term_left')
                                                                        ->columnSpan(2)
                                                                        ->label("Remaining Months")
                                                                        ->badge(),
                                                                InfoLists\Components\TextEntry::make('remaining_balance')
                                                                        ->columnSpan(2)
                                                                        ->label("Balance")
                                                                        ->money('PHP'),
                                                                InfoLists\Components\TextEntry::make('monthly_payment')
                                                                        ->columnSpan(2)
                                                                        ->label("Monthly Payment")
                                                                        ->money('PHP'),
                                                    ]),
                                        ]),
                    ]),
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
            // ->headerActions([
            //     ExportAction::make('export')->exports([
            //         ExcelExport::make('form')
            //             ->askForFilename()
            //             ->withFilename(fn ($filename) => $filename . '-' . date('M-d-Y'))
            //             ->fromTable()
            //     ]),
            // ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->button()
                    ->icon('heroicon-m-banknotes')
                    ->color('success')
                    ->label('Payment')
                    ->hidden(
                        function (Model $record) {
                            if($record->term_left == 0){
                                return true;
                            };
                            return false;
                        }
                    ),
            ])
            ->bulkActions([
                // ExportBulkAction::make(),
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
