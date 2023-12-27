<?php

namespace App\Filament\Resources;

use App\Enums\ApplicationStatus;
use App\Enums\ReleaseStatus;
use App\Filament\Resources\PaymentResource\Pages;
use App\Models\CustomerApplication;
use App\Models\Unit;
use App\Enums;
use App\Models\CustomerPaymentAccount;
use App\Models\Payment;
use Illuminate\Support\Facades\Blade;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
use Filament\Notifications;
use Illuminate\Database\Eloquent\Model;

use App\Filament\Pages\TestPage;
use App\Filament\Resources\CustomerPaymentAccountResource\Pages\ViewCustomerPaymentAccount;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    // protected static ?string $navigationLabel = 'Payments';

    // protected static ?string $recordTitleAttribute = 'name';

    // protected static ?string $modelLabel = "Payments";

    // protected static ?string $pluralModelLabel = 'Payments';

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function getApplicationInformation(): Forms\Components\Component
    {
        return Forms\Components\Group::make([
            Forms\Components\Section::make("Customer Information")
                    ->columns(12)
                    ->schema([
                        PaymentResource::getApplicationDetails()
                                ->columnSpan(12),
                        Forms\Components\TextInput::make('customerApplication.applicant_full_name')
                                ->columnSpan(6)
                                ->readOnly()
                                ->label('Full name'),
                        Forms\Components\TextInput::make('application_unit')
                                ->columnSpan(6)
                                ->readOnly()
                                ->label('Unit'),
                        Forms\Components\TextInput::make('est_monthly_payment')
                                ->label("Monthly Payment")
                                ->columnSpan(6)
                                ->readOnly(),
                    ])
        ]);
    }

    public static function getPaymentDetails(): Forms\Components\Component
    {
        return Forms\Components\Group::make([
                Forms\Components\TextInput::make("payment_status")
                        ->readOnly()
                        ->columnSpan(2),
                Forms\Components\TextInput::make('due_date')
                        ->columnSpan(6)
                        ->readOnly()
                        ->hidden(function(string $operation){
                            if($operation == "edit"){
                                return true;
                            }
                        }),
                Forms\Components\TextInput::make('payment_amount')
                        ->live()
                        ->columnSpan(2)
                        ->required()
                        ->readOnly(),
                Forms\Components\Select::make('payment_status')
                        ->options([
                            "current" => "current"
                        ])
                        ->columnSpan(2)
                        ->required(),
                Forms\Components\Select::make('payment_type')->label('Payment Type:')
                        ->options([
                            "field" => "Field",
                            "office" => "Office",
                            "bank" => "Bank",
                        ])
                        ->columnSpan(6)
                        ->required(true),
        ])
        ->columns(6);
    }

    public static function getApplicationDetails(): Forms\Components\Component
    {
        return Forms\Components\Group::make([
                Forms\Components\Select::make('customer_payment_account_id')
                        ->searchable()
                        ->getSearchResultsUsing(fn (string $search): array => CustomerPaymentAccount::query()->where('id', $search)->get()->pluck('id', 'id')->toArray())
                        ->getOptionLabelUsing(fn ($value): ?string => CustomerPaymentAccount::find($value)->id)
                        ->required()
                        ->live()
                        ->afterStateUpdated(
                            function($state, Forms\Set $set, ){
                                $account = CustomerPaymentAccount::query()->where("id", $state)->get()->first();
                                $payment_amount = 0;
                                if($account->payment_status == 'downpayment')//initial payment (Down payment)
                                {
                                    $payment_amount = $account->down_payment;
                                    $set('application_full_name', $account->customerApplication->applicant_full_name);
                                    $set('application_unit', $account->customerApplication->unitModel->model_name);
                                    $set('application_balance', $account->remaining_balance);
                                    $set('est_monthly_payment', $account->monthly_payment);
                                    $set('application_unit_price', $account->original_amount);
                                    $set('due_date', $account->calculateDueDate(Carbon::createFromFormat(config('app.date_format'), Carbon::now()->format(config('app.date_format')))));
                                }
                                else if($account->payment_status == 'monthly')//on going payment (Monthly payment)
                                {
                                    $set('application_full_name', $account->customerApplication->applicant_full_name);
                                    $set('application_unit', $account->customerApplication->unitModel->model_name);
                                    $set('application_balance', $account->remaining_balance);
                                    $set('est_monthly_payment', $account->monthly_payment);
                                    $set('application_unit_price', $account->original_amount);
                                    // $data['due_date']
                                    $set('due_date', $account->calculateDueDate(Carbon::createFromFormat(config('app.date_format'), $account->due_date)));
                                    $payment_amount = $account->monthly_payment;
                                }
                                $set('payment_amount', $payment_amount);
                                $set('status', $account->payment_status);
                            }
                        ),
                ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make([
                        PaymentResource::getApplicationInformation()
                                ->columnSpan(3),  
                ])
                ->columns(3)
                ->columnSpan(3),
                PaymentResource::getPaymentDetails()
                        ->columnSpan(3),
            ])
            ->columns(6);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                    Tables\Columns\TextColumn::make('id')
                            ->label('ID')
                            ->searchable(),
                    Tables\Columns\TextColumn::make('customerApplication.applicant_full_name')
                            ->label('Full name:')
                            ->searchable(),
                    Tables\Columns\TextColumn::make('payment_status')
                            ->label('Payment Type')
                            ->badge(),
                    Tables\Columns\TextColumn::make('status')
                            ->label('Status')
                            ->badge(),
                    Tables\Columns\TextColumn::make('monthly_payment')
                            ->label('Amount due')
                            ->money('php'),
                    Tables\Columns\TextColumn::make('remaining_balance')
                            ->label('Remaining Balance')
                            ->money('php'),
                    
            ])
            ->defaultSort('created_at', 'desc')

            ->defaultPaginationPageOption(5)
            ->filters([
                Tables\Filters\Filter::make('created_at')
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->model(CustomerPaymentAccount::class)
                    ->label('Make Payment')
                    ->color('success'),
                // Tables\Actions\Action::make('create')
                //     ->label('Make Payment')
                //     ->color('success'),
                // Tables\Actions\Action::make('pdf') 
                // ->label('Print')
                // ->color('success')
                // ->action(function (Model $record) {
                //     return response()->streamDownload(function () use ($record) {
                //         echo Pdf::loadHtml(
                //             Blade::render('monthly_amort_receipt', ['record' => $record, 'date_today' => Carbon::now()->format('d-M-Y')])
                //         )->stream();
                //     }, $record->id . '.pdf');
                // }), 
            ])
            ->bulkActions([
            ])
            ->emptyStateActions([
                // 
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/make-payment'),
            'view' => ViewCustomerPaymentAccount::route('/{record}'),
        ];
    }
}
