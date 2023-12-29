<?php

namespace App\Filament\TestPanel\Resources;

use App\Filament\TestPanel\Resources\PaymentResource\Pages;
use App\Models\CustomerApplication;
use App\Filament\TestPanel\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use App\Models;
use App\Enums;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Pages\Settings;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    
    public static function getApplicationInformation(): Forms\Components\Component
    {
        return Forms\Components\Group::make([
            Forms\Components\Section::make("Customer Application's Information")
                    ->schema([
                        Forms\Components\TextInput::make('application_full_name')
                                ->columnSpan(3)
                                ->disabled()
                                ->label('Full name'),
                        Forms\Components\TextInput::make('application_unit')
                                ->columnSpan(6)
                                ->disabled()
                                ->label('Unit'),
                        Forms\Components\TextInput::make('application_balance')
                                ->columnSpan(6)
                                ->disabled()
                                ->label('Balance'),
                        Forms\Components\TextInput::make('est_monthly_payment')
                                ->columnSpan(6)
                                ->disabled()
                                ->label('Estimated monthly payment'),
                        Forms\Components\TextInput::make('application_unit_price')
                                ->columnSpan(6)
                                ->disabled()
                                ->label('Price'),
                    ])
                    ->columns(6)
        ]);
    }
    public static function getPaymentDetails(): Forms\Components\Component
    {
        return Forms\Components\Group::make([
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
                        ->readOnly(function (Forms\Get $get):bool{
                            $dp = CustomerApplication::query()
                                ->where('id', $get('customer_application_id'))
                                ->first();
                            if($dp != null){
                                return true;
                            }
                            return false;
                        }),
                Forms\Components\Select::make('payment_status')
                        ->live()
                        ->options([
                            'advance' => 'Advance',
                            'current' => 'Current',
                            'overdue' => 'Overdue',
                            'diligent' => 'Diligent',
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

    public static function getPaymentInput(): Forms\Components\Component
    {
        return Forms\Components\Group::make([
        ]);
    }

    public static function getApplicationDetails(): Forms\Components\Component
    {
        return Forms\Components\Group::make([
                Forms\Components\Select::make('customer_application_id')
                ->relationship(
                        name: 'customerApplication',
                        titleAttribute: 'applicant_lastname',
                        modifyQueryUsing: fn (Builder $query) => $query->where("application_status", "Active")
                                ->orwhere("application_status", "Approved"),
                )
                ->label('For Applicant:')
                ->preload()
                ->searchable()
                ->required()
                ->live()
                ->afterStateUpdated(
                    function($state, Forms\Set $set, ?Model $record){
                        $application = CustomerApplication::query()
                                ->where("id", $state)
                                ->first();
                        $set('due_date', "");
                        $set('payment_amount', "");
                        $set('application_firstname',  "");
                        $set('customer_application_group',  "");
                        $set('application_unit',  "");
                        $set('application_unit',  "");
                        $set('application_unit_price',  "");
                        if($application != null){
                            if($application->application_status == Enums\ApplicationStatus::APPROVED_STATUS ->value
                                    && $application->release_status == Enums\ReleaseStatus::UN_RELEASED->value)
                            {
                                dd("Down Payment");
                            }else if($application->application_status == Enums\ApplicationStatus::ACTIVE_STATUS->value){
                                // dd("Amort. Payment");
                                $state->payment_amount = $application->unit_monthly_amort;
                                dd($record->payment_amount);
                            }
                            $due_date = $application->due_date;
                            $today = Carbon::today();
                            
                            $amort_fin = $application->unit_monthly_amort;
                            $set('due_date', $due_date);
                            $set('payment_amount', $amort_fin);
                            $set('application_firstname', $application->applicant_firstname);
                            $set('application_lastname', $application->applicant_lastname);
                            $set('application_unit', $application->unitModel->model_name);
                            $set('application_unit_price', $application->unitModel->price);
                            
                            $delinquent = $today->copy()->addDays(30);
                            
                            $parsed_date = Carbon::createFromFormat(config('app.date_format'), $due_date);
                            
                            $is_advance = $today->lt($parsed_date);
                            $is_current = $today->eq($parsed_date);
                            $is_overdue = $today->gt($parsed_date) && $today->lt($delinquent);
                            $is_delinquent = $today->gt($delinquent);
    
                            if($today->lessThan($parsed_date)){
                                $set('payment_status', 'advance');
                            }
                            elseif($today->equalTo($parsed_date)){
                                $set('payment_status', 'current');
                            }
                            elseif($today->greaterThan($parsed_date) && $today->lessThan($delinquent)){
                                $set('payment_status', 'overdue');
                            }
                            elseif($today->greaterThan($delinquent)){
                                $set('payment_status', 'delinquent');
                            }
                        }
                    }
                ),
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Group::make([
                PaymentResource::getApplicationDetails()
                        ->columnSpan(3),
                PaymentResource::getApplicationInformation()
                        ->columnSpan(3),  
            ])
            ->columns(3)
            ->columnSpan(3),
            PaymentResource::getPaymentDetails()
                    ->columnSpan(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                ->label('ID'),
                Tables\Columns\TextColumn::make('payment_amount')
                ->label('Payment Amount')
                ->money('php'),
                Tables\Columns\TextColumn::make('created_at')
                ->label('Date Paid')
                ->dateTime('d-M-Y'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            // 'create' => Pages\CreatePayment::route('/create'),
            'create' => Pages\CreatePaymongoCheckout::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('customer_payment_account_id', auth()->id());
    }
}
