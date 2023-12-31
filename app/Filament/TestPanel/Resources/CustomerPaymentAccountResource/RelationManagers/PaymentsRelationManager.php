<?php

namespace App\Filament\TestPanel\Resources\CustomerPaymentAccountResource\RelationManagers;

use App\Models;
use App\Enums;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
use Filament\Forms\Components\Wizard\Step;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // ..
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('payment_amount')
                    ->money('PHP'),
                Tables\Columns\TextColumn::make('customer_is')
                    ->label("Payment Is"),
                Tables\Columns\TextColumn::make('rebate')
                    ->money('PHP')
                    ->label("Rebate"),
                Tables\Columns\TextColumn::make('created_at')
                    ->date()
                    ->label("Payment Date"),
            ])
            ->filters([
            //
            ])
            ->headerActions([
                Tables\Actions\Action::make('Make Payment')
                    ->disabled(fn (RelationManager $livewire) => $livewire->getOwnerRecord()->term_left == 0)
                    ->steps([
                            Step::make('Make Payment')
                                    ->description('Enter payment details')
                                    ->schema([
                                            static::getPaymentInputComponent()
                                                    ->columns(12)
                                                    ->columnSpan(12),
                                    ])
                                    ->columns(4),
                            Step::make('Review')
                                    ->description('Review the payment being made.')
                                    ->schema([
                                            static::getPaymentReviewComponent()
                                                    ->columns(12)
                                                    ->columnSpan(12)
                                    ])
                                    ->columns(4),
                        ])
                        ->action(fn(RelationManager $livewire, array $data) => redirect()->route('paymongo', [
                                'customerPaymentAccount' => http_build_query($livewire->getOwnerRecord()->getAttributes()),
                                'payment' => http_build_query($data),
                        ])),
            ])
            ->actions([
                Tables\Actions\ViewAction::make('view'),
                Tables\Actions\Action::make('pdf') 
                ->label('Print')
                ->color('success')
                ->action(function (Model $record, RelationManager $livewire) {
                    return response()->streamDownload(function () use ($record, $livewire) {
                        echo Pdf::loadHtml(
                            Blade::render('monthly_amort_receipt', ['record' => $record, 'customer' => $livewire->getOwnerRecord()->customerApplication, 'date_today' => Carbon::now()->format('d-M-Y')])
                        )
                        ->setPaper('A4', 'landscape')
                        ->stream();
                    }, $record->id . '.pdf');
                }), 
            ])
            ->bulkActions([
                // ..
            ]);
    }

    public function getPaymentReviewComponent(): Forms\Components\Group
    {
        return Forms\Components\Group::make([

            Forms\Components\Section::make('Account Details')
                ->columns(12)
                ->description('You are paying for this account')
                ->schema([
                    Forms\Components\Placeholder::make('plan')
                        ->label('Applicant Full Name')
                        ->columnSpan(4)
                        ->content(function (Forms\Get $get, RelationManager $livewire): string {
                            return $livewire->getOwnerRecord()->customerApplication->applicant_full_name;
                        }),
                    Forms\Components\Placeholder::make('unit')
                        ->label('Unit')
                        ->columnSpan(4)
                        ->content(function (Forms\Get $get, RelationManager $livewire): string {
                            return $livewire->getOwnerRecord()->customerApplication->unitModel->model_name;
                        }),
                    Forms\Components\Placeholder::make('plan')
                        ->label('Plan')
                        ->columnSpan(4)
                        ->content(function (Forms\Get $get, RelationManager $livewire): string {
                            return  $livewire->getOwnerRecord()->customerApplication->plan->value;
                        }),
                ]),
            Forms\Components\Section::make('Payment Details')
                ->description('review the details before proceeding')
                ->columns(12)
                ->schema([
                    Forms\Components\Placeholder::make('')
                        ->label('Term Covered')
                        ->columnSpan(4)
                        ->content(function (Forms\Get $get): string {
                            return $get('term_covered');
                        }),
                    Forms\Components\Placeholder::make('')
                        ->label('Customer Is')
                        ->columnSpan(4)
                        ->content(function (Forms\Get $get): string {

                            return $get('customer_is');
                        }),
                    Forms\Components\Placeholder::make('')
                        ->label('Payment Is')
                        ->columnSpan(4)
                        ->content(function (Forms\Get $get): string {
                            return $get('payment_is');
                        }),
                    Forms\Components\Placeholder::make('')
                        ->label('Payment Amount')
                        ->columnSpan(4)
                        ->content(function (Forms\Get $get): string {
                            return $get('payment_amount');
                        }),
                    Forms\Components\Placeholder::make('')
                        ->label('Change')
                        ->columnSpan(4)
                        ->content(function (Forms\Get $get): string {
                            return $get('change');
                        }),
                    Forms\Components\Placeholder::make('')
                        ->label('Amount to be paid')
                        ->columnSpan(4)
                        ->content(function (Forms\Get $get): string {
                            return $get('amount_to_be_paid');
                        }),
                    Forms\Components\Placeholder::make('')
                        ->label('Term Covered')
                        ->columnSpan(4)
                        ->content(function (Forms\Get $get): string {
                            return $get('payment_type');
                        }),
                ]),
        ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    public function getPaymentInputComponent(): Forms\Components\Group
    {
        return Forms\Components\Group::make([
            Forms\Components\TextInput::make("terms_left")
                ->label('Term left')
                ->default(
                    function (RelationManager $livewire): string {
                        $owner_record = $livewire->getOwnerRecord();
                        return $owner_record->term_left;
                    }
                )
                ->numeric()
                ->live()
                ->readOnly()
                ->columnSpan(3),
            Forms\Components\TextInput::make("payment_is")
                ->readOnly()
                ->default(
                    function (RelationManager $livewire): string {
                        $owner_record = $livewire->getOwnerRecord();
                        return $owner_record->payment_status->value;
                    }
                )
                ->columnSpan(3),
            Forms\Components\TextInput::make("payment_type")
                ->readOnly()
                ->default(
                    function (): string {
                        return 'Online';
                    }
                )
                ->columnSpan(3),
            Forms\Components\TextInput::make("customer_is")
                ->readOnly()
                ->default(
                    function (RelationManager $livewire): string {
                        $owner_record = $livewire->getOwnerRecord();
                        if ($owner_record->payment_status->value == Enums\PaymentStatus::DOWN_PAYMENT->value) {
                            $set_due = Models\Payment::calculateDueDate(Carbon::now());
                            $owner_record->due_date = $set_due->toDateString();
                            return Enums\PaymentStatus::CURRENT->value;
                        } else if ($owner_record->payment_status->value == Enums\PaymentStatus::CASH->value) {
                            return Enums\PaymentStatus::CURRENT->value;
                        } else if ($owner_record->payment_status->value == Enums\PaymentStatus::MONTHLY->value) {
                            $now = Carbon::now();
                            $due_date = Carbon::parse($owner_record->due_date);

                            // Compare the current time and due date
                            if ($now->lessThan($due_date)) {
                                // Advance (current time is less than due date)
                                return Enums\PaymentStatus::ADVANCED->value;
                            } elseif ($now->equalTo($due_date)) {
                                // Current (current time is equal to due date)
                                return Enums\PaymentStatus::CURRENT->value;
                            } elseif ($now->greaterThan($due_date)) {
                                // Overdue (current time is past due date)

                                // Check if it's one or two months overdue
                                $monthsOverdue = $now->diffInMonths($due_date);
                                if ($monthsOverdue == 1) {
                                    return Enums\PaymentStatus::OVERDUE->value;
                                } elseif ($monthsOverdue == 2) {
                                    return Enums\PaymentStatus::DELINQUENT->value;
                                } else {
                                    // Handle other cases as needed
                                    return Enums\PaymentStatus::OVERDUE->value;
                                }
                            }
                        }
                        return "";
                    }
                )
                ->columnSpan(3),
            Forms\Components\TextInput::make("rebate")
                ->readOnly()
                ->default(
                    function (RelationManager $livewire): float {
                        $owner_record = $livewire->getOwnerRecord();
                        if ($owner_record->payment_status->value == Enums\PaymentStatus::DOWN_PAYMENT->value) {
                            $set_due = Models\Payment::calculateDueDate(Carbon::now());
                            $owner_record->due_date = $set_due->toDateString();
                            return 0;
                        } else if ($owner_record->payment_status->value == Enums\PaymentStatus::CASH->value) {
                            return 400.00;;
                        } else if ($owner_record->payment_status->value == Enums\PaymentStatus::MONTHLY->value) {
                            $now = Carbon::now();
                            $due_date = Carbon::parse($owner_record->due_date);

                            // Compare the current time and due date
                            if ($now->lessThan($due_date)) {
                                // Advance (current time is less than due date)
                                return 400.00;
                            } elseif ($now->equalTo($due_date)) {
                                // Current (current time is equal to due date)
                                return 400.00;
                            } elseif ($now->greaterThan($due_date)) {
                                return 0.00;
                            }
                        }
                        return 0.00;
                    }
                )
                ->label('Rebate')
                ->columnSpan(3),
            Forms\Components\TextInput::make("amount_to_be_paid")
                ->label('Amount Due')
                ->numeric()
                ->live()
                ->readOnly()
                ->columnSpan(6)
                ->default(
                    function (RelationManager $livewire): float {
                        $owner_record = $livewire->getOwnerRecord();
                        if ($owner_record->payment_status->value == Enums\PaymentStatus::DOWN_PAYMENT->value) {
                                return $owner_record->down_payment;
                        }
                        else if ($owner_record->payment_status->value == Enums\PaymentStatus::CASH->value) {
                                return $owner_record->unit_ttl_dp;
                        } else if ($owner_record->payment_status->value == Enums\PaymentStatus::MONTHLY->value) {
                                return $owner_record->monthly_payment;
                        } else {
                                return $owner_record->remaining_balance;
                        }
                    }
                ),
            Forms\Components\TextInput::make('term_covered')
                ->label("Term covered.")
                ->readOnly(
                    function (RelationManager $livewire): int {
                        $owner_record = $livewire->getOwnerRecord();
                        if ($owner_record->payment_status->value == Enums\PaymentStatus::DOWN_PAYMENT->value || $owner_record->payment_status == Enums\PaymentStatus::CASH->value) {
                            return true;
                        }
                        return false;
                    }
                )
                ->minvalue(
                    function (RelationManager $livewire): int {
                        $owner_record = $livewire->getOwnerRecord();
                        if ($owner_record->payment_status->value == Enums\PaymentStatus::DOWN_PAYMENT->value) {
                            return 0;
                        }
                        return 1;
                    }
                )
                ->maxvalue(
                    function (RelationManager $livewire): int {
                        $owner_record = $livewire->getOwnerRecord();
                        return $owner_record->term_left;
                    }
                )
                ->live()
                ->default(
                    function (RelationManager $livewire): int {
                        $owner_record = $livewire->getOwnerRecord();
                        if ($owner_record->payment_status->value == Enums\PaymentStatus::DOWN_PAYMENT->value) {
                            return 0;
                        }
                        return 1;
                    }
                )
                ->required()
                ->numeric()
                ->columnSpan(2)
                ->afterStateUpdated(
                    function (Forms\Get $get, Forms\Set $set, RelationManager $livewire, Forms\Components\TextInput $component) {
                        $term = (int)$get('term_covered');
                        if ($term >= 1) {
                            $monthly_payment = $livewire->getOwnerRecord()->monthly_payment;
                            $product = $monthly_payment * $get('term_covered');
                            $set('amount_to_be_paid', $product);
                            $set('payment_amount', $product - $get('rebate'));
                        }
                        $livewire->validateOnly($component->getStatePath());
                    }
                ),
            Forms\Components\TextInput::make('payment_amount')
                ->label('Amount Paid')
                ->required()
                ->live(onBlur: true)
                ->numeric()
                ->readOnly()
                ->default(function (Forms\Get $get) {
                    return $get('amount_to_be_paid') - $get('rebate');
                })
                ->columnSpan(4)
                ->minValue(fn (Forms\Get $get): float => $get('amount_to_be_paid') - $get('rebate')),
            Forms\Components\TextInput::make('change')
                ->label('Change')
                ->hidden(true)
                ->readOnly()
                ->live()
                ->default(0)
                ->columnSpan(4)
        ]);
    }
}
