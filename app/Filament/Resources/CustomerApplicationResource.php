<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerApplicationResource\Pages;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Summarizers\Average;
use App\Filament\Resources\CustomerApplicationResource\RelationManagers;
use App\Models\CustomerApplication;
use App\Models;
use App\Enums;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use Filament\Support\Enums\Alignment;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Wizard;
use Illuminate\Support\Facades\Blade;

use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;

class CustomerApplicationResource extends Resource
{
    protected static ?string $model = CustomerApplication::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function canDelete(Model $record): bool
    {
        return true;
    }

    public static function getUnitToBeFinanced(): Forms\Components\Component
    {
            return Forms\Components\Group::make([
                    Forms\Components\Fieldset::make("Unit to be Financed")
                    ->columns(4)
                    ->columnSpan(2)
                    ->schema([
                            Forms\Components\Select::make('unit_model_id')
                                    ->columnSpan(2)
                                    ->required()
                                    ->label('Unit Model')
                                    ->relationship(
                                                    'unitModel',
                                                    'model_name'
                                                    )
                                    ->searchable(['model_name', 'id'])
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(
                                            function(Forms\Get $get, Forms\Set $set){
                                                    $unit_model = Models\UnitModel::find($get("unit_model_id"));
                                                    $unit_monthly_amort_fin = Models\Payment::calculateAmountMonthlyPayment(
                                                        $unit_model->price,
                                                        $unit_model->down_payment_amount,
                                                        $get('unit_term'),
                                                        0.05,
                                                    );
                                                    $set('unit_srp', $unit_model->price);
                                                    $set('unit_monthly_amort_fin', $unit_monthly_amort_fin);
                                                    $set('unit_ttl_dp', $unit_model->down_payment_amount);
                                            }
                                    ),
                            Forms\Components\Select::make('unit_term')
                                    ->columnSpan(2)
                                    ->live()
                                    ->required()
                                    ->label("Term/Months")
                                    ->default(36)
                                    ->options([
                                        36 => 'Thirty six (36)',
                                        30 => 'Thirty (30)',
                                        24 => 'Twenty four (24)',
                                        18 => 'Eighteen (18)',
                                        12 => 'Twelve (12)',
                                    ])
                                    ->afterStateUpdated(
                                        function(Forms\Get $get, Forms\Set $set){
                                                $unit_model = Models\UnitModel::find($get("unit_model_id"));
                                                $unit_monthly_amort_fin = Models\Payment::calculateAmountMonthlyPayment(
                                                    $unit_model->price,
                                                    $unit_model->down_payment_amount,
                                                    $get('unit_term'),
                                                    0.05,
                                                );
                                                $set('unit_srp', $unit_model->price);
                                                $set('unit_monthly_amort_fin', $unit_monthly_amort_fin);
                                                $set('unit_ttl_dp', $unit_model->down_payment_amount);
                                        }
                                ),
                            Forms\Components\TextInput::make('unit_ttl_dp')
                                    ->columnSpan(2)
                                    ->readOnly()
                                    ->label('Down payment'),
                            Forms\Components\TextInput::make('unit_monthly_amort_fin')
                                    ->columnSpan(2)
                                    ->label('Monthly amortization'),
                            Forms\Components\Select::make('preffered_unit_status')
                                    ->columnSpan(2)
                                    ->required()
                                    ->label("Preffered unit status")
                                    ->options(Enums\UnitStatus::class),
                            Forms\Components\Select::make('plan')
                                    ->columnSpan(1)
                                    ->required()
                                    ->label("Plan")
                                    ->options([
                                        'cash' => 'Cash',
                                        'installment' => 'Installment',
                                    ]),
                            Forms\Components\Group::make()
                                    ->columnSpan(4)
                                    ->columns(2)
                                    ->live()
                                    ->disabled(fn (Forms\Get $get): bool => ! $get('unit_model_id'))
                                    ->schema([
                                            Forms\Components\TextInput::make('unit_srp')
                                                    ->columnSpan(2)
                                                    ->readOnly()
                                                    ->required(true)
                                                    ->label('Selling Retail Price:')
                            
                                    ]),
                    ]),
            ]);
    }

    public static function getCoOwnerInformation(): Forms\Components\Component
    {
        return Forms\Components\Section::make("Co-Maker")
                ->schema([
                        Forms\Components\Group::make([
                                Forms\Components\Group::make([
                                        Forms\Components\TextInput::make('co_owner_firstname')
                                        ->columnSpan(2)
                                        ->label('First Name:')
                                        ->required(true),
                                Forms\Components\TextInput::make('co_owner_middlename')
                                        ->columnSpan(2)
                                        ->label('Middle Name:'),
                                Forms\Components\TextInput::make('co_owner_lastname')
                                        ->columnSpan(2)
                                        ->label('Last Name:')
                                        ->required(true),
                                Forms\Components\TextInput::make('co_owner_email')
                                        ->columnSpan(6)
                                        ->label('Email:'),
                                Forms\Components\DatePicker::make('co_owner_birthday')
                                        ->columnSpan(3)
                                        ->label('Birthday:')
                                        ->maxDate(now()->subYears(150))
                                        ->maxDate(now())
                                        ->required(true),
                                Forms\Components\TextInput::make('co_owner_mobile_number')
                                        ->columnSpan(3)
                                        ->label('Telephone:'),
                                Forms\Components\TextArea::make('co_owner_address')
                                        ->columnSpan(6)
                                        ->label('Address')
                                        ->required(true),
                                ])
                                ->columnSpan(3)
                                ->columns(6),
                                Forms\Components\Fileupload::make('co_owner_valid_id')
                                        ->image()
                                        ->disk('public')
                                        ->multiple(true)
                                        ->minFiles(2)
                                        ->maxFiles(2)
                                        ->hint("Requires two (2) valid ID's")
                                        ->label('Valid ID:')
                                        ->required(true)
                                        ->columnSpan(3),
                        ])
                        ->columns(6)
                        ->columnSpan(6),
                ])
                ->columns(6);
    }
    public static function getApplicantInformation(): Forms\Components\Component
    {
                return Forms\Components\Group::make([
                        Forms\Components\Group::make([
                                Forms\Components\TextInput::make('applicant_firstname')
                                        ->label('First Name:')
                                        ->columnSpan(2)
                                        ->required(true),
                                Forms\Components\TextInput::make('applicant_middlename')
                                        ->label('Middle Name:')
                                        ->columnSpan(2),
                                Forms\Components\TextInput::make('applicant_lastname')
                                        ->label('Last Name:')
                                        ->columnSpan(2)
                                        ->required(true),
                                Forms\Components\DatePicker::make('applicant_birthday')
                                        ->label('Birthday:')
                                                ->maxDate(now()->subYears(150))
                                                ->maxDate(now())
                                        ->columnSpan(3)
                                        ->required(true),
                                Forms\Components\Select::make('applicant_civil_status')
                                        ->label('Civil Status')
                                        ->live()
                                        ->columnSpan(3)
                                        ->required(true)
                                        ->options(['single'=> 'Single', 'married' => 'Married', 'separated' => 'Separated', 'widow' => 'Widow']),
                        ])
                        ->columnSpan(3)
                        ->columns(6),

                        Forms\Components\Fieldset::make("Contact")
                            ->schema([
                                Forms\Components\TextInput::make('applicant_telephone')
                                        ->columnSpan(3)
                                        ->label('Telephone:')
                                        ->required(),
                                Forms\Components\TextInput::make('email')
                                        ->columnSpan(3)
                                        ->required()
                                        ->label('Email:'),
                            ])
                        ->columnSpan(3)
                        ->columns(6),

                        Forms\Components\Group::make([
                                Forms\Components\TextInput::make('applicant_lived_there')
                                        ->columnSpan(3)
                                        ->numeric()
                                        ->suffix('year(s)')
                                        ->inputMode('integer')
                                        ->label('Lived There:'),
                                Forms\Components\Select::make('applicant_house')
                                        ->columnSpan(3)
                                        ->label('House:')
                                        ->options(['owned' => 'Owned', 'rented' => 'Rented', 'w/ parents' => 'W/ parents']),
                                Forms\Components\Textarea::make('applicant_present_address')
                                        ->columnSpan(6)
                                        ->label('Present Address:')
                                        ->required(true),
                                Forms\Components\Textarea::make('applicant_previous_address')
                                        ->columnSpan(6)
                                        ->label('Previous Address:'),
                                Forms\Components\Textarea::make('applicant_provincial_address')
                                        ->columnSpan(6)
                                        ->label('Provincial Address:'),
                        ])
                        ->columnSpan(3)
                        ->columns(6),
                        Forms\Components\Fileupload::make('applicant_valid_id')
                                ->multiple(true)
                                ->disk('public')
                                ->directory('applicant_valid_id')
                                ->hint("Requires two (2) valid ID's")
                                ->minFiles(2)
                                ->maxFiles(2)
                                ->label('Valid ID:')
                                ->required(true)
                                ->columnSpan(3),
                ])
                ->columns(6);
    }

    public static function getSpouseComponents(): Forms\Components\Component
    {
        return Forms\Components\Group::make([
                Forms\Components\Section::make("Spouse Information")
                        ->schema([
                            Forms\Components\Fileupload::make('spouse_valid_id')
                                    ->multiple(true)
                                    ->label('Valid ID:')
                                    ->required(true)
                                    ->columnSpan(3)
                                    ->columns(6),
                            Forms\Components\Group::make([
                                    Forms\Components\TextInput::make('spouse_firstname')
                                            ->label('First name')
                                            ->columnSpan(2)
                                            ->required(true),
                                    Forms\Components\TextInput::make('spouse_middlename')
                                            ->label('Middle Name')
                                            ->columnSpan(2),
                                    Forms\Components\TextInput::make('spouse_lastname')
                                            ->label('Last Name')
                                            ->columnSpan(2)
                                            ->required(true),
                                    Forms\Components\DatePicker::make('spouse_birthday')
                                            ->label('Birthday')
                                            ->columnSpan(3)
                                            ->required(true),
                                    Forms\Components\TextInput::make('spouse_telephone')
                                            ->label('Telephone')
                                            ->columnSpan(3),
                                    Forms\Components\Textarea::make('spouse_present_address')
                                            ->columnSpan(3)
                                            ->label('Present Address')
                                            ->required(true),
                                    Forms\Components\Textarea::make('spouse_provincial_address')
                                            ->columnSpan(3)
                                            ->label('Provincial Address'),
                            ])
                            ->columnSpan(3)
                            ->columns(6),
                ])
                ->columnSpan(6)
                ->columns(6),
        ])
        ->hidden(fn (Forms\Get $get): bool => $get('applicant_civil_status') != "married");
    }

    public static function getEducationalAttainment(): Forms\Components\Component
    {
                return Forms\Components\Group::make([
                                Forms\Components\Repeater::make("educational_attainment")
                                        ->schema([
                                                Forms\Components\TextInput::make("course")
                                                        ->label("Degree")
                                                        ->columnSpan(2),
                                                Forms\Components\TextInput::make("no_years")
                                                        ->columnSpan(1)
                                                        ->numeric()
                                                        ->suffix("year(s)"),
                                                Forms\Components\TextInput::make("school")
                                                        ->columnSpan(2),
                                                Forms\Components\DatePicker::make("year_grad")
                                                        ->columnSpan(1),
                                        ])
                                        ->columns(3)
                                        ->columnSpan(3),
                                Forms\Components\Repeater::make("dependents")
                                        ->schema([
                                            Forms\Components\TextInput::make("dependent_name")
                                                    ->columnSpan(2)
                                                    ->label("Name"),
                                            Forms\Components\DatePicker::make("dependent_birthdate")
                                                    ->columnSpan(1)
                                                    ->label("Birthdate"),
                                            Forms\Components\TextInput::make("dependent_school")
                                                    ->columnSpan(2)
                                                    ->label("School"),
                                            Forms\Components\TextInput::make("dependent_monthly_tuition")
                                                    ->columnSpan(1)
                                                    ->numeric()
                                                    ->label("Tuition"),
                                        ])
                                        ->columns(3)
                                        ->columnSpan(3),


                ])
                ->columnSpan(6)
                ->columns(6);
    }

	public static function getReferences(): Forms\Components\Component
    {
        return Forms\Components\Group::make([
                Forms\Components\Fieldset::make('Financial References')
                ->columns(1)
                ->schema([
						Forms\Components\Group::make([
								Forms\Components\Fieldset::make('Credit Card Information')
								->columns(1)
								->schema([
										Forms\Components\Select::make('bank_acc_type')
												->required()
												->columnSpan(2)
												->options([
														'time_deposit' => 'Time Deposit',
														'savings' => 'Savings',
												]),
										Forms\Components\TextInput::make('account_number')->numeric()
														->required()
														->minLength(12)
														->hint('Card number must be of exact twelve (12) digits.')
														->maxLength(12)
														->columnSpan(1),
										Forms\Components\TextInput::make('bank_or_branch')
														->required()
														->columnSpan(2),
										Forms\Components\DatePicker::make('date_openned')
														->required()
														->minDate(now()->subYears(150))
														->maxDate(now()),
										Forms\Components\TextInput::make('average_monthly_balance')
														->required()
														->numeric(),
								]),
								Forms\Components\Fieldset::make('Credit Card')
												->columns(1)
												->schema([
														Forms\Components\Group::make([
																		Forms\Components\TextInput::make('credit_card_company')
																						->required(),
																		Forms\Components\TextInput::make('card_number')
																						->required()
																						->minLength(12)
																						->hint('Card number must be of exact twelve (12) digits.')
																						->maxLength(12)
																						->columnSpan(2),
																		Forms\Components\DatePicker::make('date_issued')
																						->required()
																						->columnSpan(2)
																						->minDate(now()->subYears(150))
																						->maxDate(now()),
																		Forms\Components\TextInput::make('credit_limit')
																						->required()
																						->columnSpan(2),
																		Forms\Components\TextInput::make('outstanding_balance')
																						->required()
																						->columnSpan(2),
																		])->columns(4),
																]),

								Forms\Components\Fieldset::make('Creditor Information')
												->columns(1)
												->schema([
																Forms\Components\TextInput::make('creditor')
																				->required()
																				->label('Creditor Name:')
																				->required()
																				->columnSpan(4),
																Forms\Components\TextInput::make('term')
																				->required()
																				->columnSpan(2),
																Forms\Components\TextInput::make('present_balance')
																				->required()
																				->columnSpan(2)
																				->numeric(),
																Forms\Components\TextInput::make('principal')
																				->required()
																				->columnSpan(2),
																Forms\Components\TextInput::make('monthly_amorthization')
																				->required()
																				->columnSpan(2)
																				->numeric(),
												])->columns(4),
						]),
                ]),

                Forms\Components\Repeater::make('personal_references')
                        ->columnSpanFull()
						->required()
						->columnSpan(1)
                        ->columns(4)
                        ->label('Applicant\'s Personal References')
                        ->collapsible(true)
                        ->schema([
                                Forms\Components\TextInput::make('name'),
                                Forms\Components\TextInput::make('address'),
                                Forms\Components\TextInput::make('relationship')
                                        ->autocomplete(true)
                                        ->datalist([
                                                'Mother',
                                                'Father',
                                                'Spouse',
                                                'Husband',
                                        ]),
                                Forms\Components\TextInput::make('telephone')->numeric(),
                ]),
        ]);
    }

    public static function getEmployment(): Forms\Components\Component
    {
        return Forms\Components\Group::make([
            Forms\Components\Fieldset::make("Present Employer")
                    ->schema([
                            Forms\Components\TextArea::make('applicant_present_business_employer')
                                    ->label('Business Employer:')
                                    ->required()
                                    ->columnSpan(2),
                            Forms\Components\TextInput::make('applicant_position')
									->required()
                                    ->label('Position:')
                                    ->columnSpan(1),
                            Forms\Components\TextInput::make('applicant_how_long_job_or_business')
									->required()
                                    ->label('How long:')
                                    ->columnSpan(1),
                    ]),
            Forms\Components\Fieldset::make("Applicant's Business")
                    ->columnSpan(1)
                    ->columns(1)
                    ->schema([
                            Forms\Components\TextArea::make('applicant_business_address')
									->required()
                                    ->label('Address:')
                                    ->columnSpan(1),
                            Forms\Components\TextInput::make('applicant_nature_of_business')
									->required()
                                    ->label('Nature of Business:'),
                    ]),                                
            Forms\Components\Fieldset::make("Previous Employer")
                    ->columns(3)
                    ->columnSpan(3)
                    ->schema([
                            Forms\Components\TextArea::make('applicant_previous_employer')
									->required()
                                    ->label('Employer:')
                                    ->columnSpan(1),
                            Forms\Components\TextArea::make('applicant_previous_employer_position')
									->required()
                                    ->label('Position:')
                                    ->columnSpan(1),
                            Forms\Components\TextArea::make('applicant_how_long_prev_job_or_business')
									->required()
                                    ->label('How Long:')
                                    ->columnSpan(1),
                    ]),
            Forms\Components\Fieldset::make("Spouse's Employement Information")
                    ->schema([
                            Forms\Components\Fieldset::make("Present Employer")
                            ->columns(2)
                            ->columnSpan(2)
                            ->schema([
                                    Forms\Components\TextArea::make('spouse_employer')
                                            ->label('Business Employer:')
                                            ->columnSpan(2),
                                    Forms\Components\TextInput::make('spouse_position')
                                            ->label('Position:')
                                            ->columnSpan(1),
                                    Forms\Components\TextInput::make('spouse_how_long_job_business')
                                            ->label('How long:')
                                            ->columnSpan(1),
                            ]),
                            Forms\Components\Fieldset::make("Business")
                                    ->columnSpan(1)
                                    ->columns(1)
                                    ->schema([
                                            Forms\Components\TextArea::make('spouse_business_address')->label('Address:')->columnSpan(1),
                                            Forms\Components\TextInput::make('spouse_nature_of_business')->label('Nature of Business:'),
                                    ]),  
                    ])->hidden(fn (Forms\Get $get): bool => $get('applicant_civil_status') != "married")

        ]);
    }

    public static function getProperties(): Forms\Components\Component
    {
        return Forms\Components\Group::make([
            Forms\Components\Repeater::make("properties")
                    ->live()
                    ->schema([
                            Forms\Components\Select::make('type_of_property')
                                    ->live()
                                    ->columnSpan(2)
                                    ->label("Type of properties:")
                                    ->options([
                                            'vehicle' => 'Vehicle',
                                            'house' => 'House',
                                            'lot' => 'Lot',
                                            'appliance' => 'Appliance',
                                    ])
                                    ->afterStateUpdated(
                                            fn (Forms\Components\Select $component) => $component
                                                ->getContainer()
                                                ->getComponent('dynamicFields')
                                                ->getChildComponentContainer()
                                                ->fill()
                                    ),

                            Forms\Components\Grid::make(2)
                                    ->key('dynamicFields')
                                    ->schema(
                                            fn (Forms\Get $get): array => match ($get('type_of_property')){
                                                'vehicle' => [],
                                                'house' => [
                                                    Forms\Components\TextInput::make('clean'),
                                                    Forms\Components\TextInput::make('mortgage'),
                                                    Forms\Components\TextInput::make('to_whom'),
                                                    Forms\Components\TextInput::make('market_value'),
                                                ],
                                                'lot' => [
                                                    Forms\Components\TextInput::make('clean'),
                                                    Forms\Components\TextInput::make('mortgage'),
                                                    Forms\Components\TextInput::make('to_whom'),
                                                    Forms\Components\TextInput::make('market_value'),
                                                ],
                                                'appliance' => [
                                                    Forms\Components\TextInput::make('name'),
                                                ],
                                                default => [],
                                            }
                                    ),
                    ])->columns(2),

        ]);
    }
    public static function getIncome(): Forms\Components\Component
    {
        return new Forms\Components\Group([
                
                Forms\Components\Group::make([
                        Forms\Components\Fieldset::make("Applicant's Income")
                        ->columns(4)
                        ->columnSpan(2)
                        ->schema([
                                Forms\Components\TextInput::make("applicants_basic_monthly_salary")
                                        ->label("Basic Monthly Salary:")
                                        ->minLength(4)
                                        ->maxLength(7)
                                        ->required()
                                        ->default(0)
                                        ->numeric()
                                        ->columnSpan(1),
                                Forms\Components\TextInput::make("applicants_allowance_commission")
                                        ->label("Allowance Commision:")
                                        ->required()
                                        ->default(0)
                                        ->numeric()
                                        ->columnSpan(1),
                                Forms\Components\TextInput::make("applicants_deductions")
                                        ->label("Deductions:")
                                        ->required()
                                        ->default(0)
                                        ->numeric()
                                        ->columnSpan(1),
                                Forms\Components\TextInput::make("applicants_net_monthly_income")
                                        ->label("Net Monthly Income:")
                                        ->default(0)
                                        ->numeric()
                                        ->columnSpan(1),
                        ]),


                        Forms\Components\Fieldset::make("Spouse's Monthly Salary")
                                ->columns(4)
                                ->columnSpan(2)
                                ->disabled(fn (Forms\Get $get): bool => $get('applicant_civil_status') != "married")
                                ->schema([
                                        Forms\Components\TextInput::make("spouses_basic_monthly_salary")->label("Basic Monthly Salary:")
                                                ->columnSpan(1)
                                                ->default(0)
                                                ->numeric(),
                                        Forms\Components\TextInput::make("spouse_allowance_commision")->label("Allowance Commision:")->numeric()
                                                ->columnSpan(1)
                                                ->default(0)
                                                ->numeric(),
                                        Forms\Components\TextInput::make("spouse_deductions")->label("Deductions:")->numeric()
                                                ->columnSpan(1)
                                                ->default(0)
                                                ->numeric(),
                                        Forms\Components\TextInput::make("spouse_net_monthly_income")->label("Net Monthly Income:")->numeric()
                                                ->columnSpan(1)
                                                ->default(0)
                                                ->numeric(),
                                ]),
                ])
                ->columns(2),

                Forms\Components\TextInput::make("other_income")->label("Other Income:")->numeric()
                        ->columnSpan(1)
                        ->default(0)
                        ->default(0),

                Forms\Components\TextInput::make("gross_monthly_income")->label("Gross Monthly Income:")->numeric()->columnSpan(1)
                        ->numeric()
                        ->columnSpan(1)
                        ->default(0)
                        ->prefix('Total:'),

                Forms\Components\Actions::make([
                        Forms\Components\Actions\Action::make('Calculate Income')
                                ->icon('heroicon-m-calculator')
                                ->action(function (Forms\Set $set, Forms\Get $get){

                                    //Net Monthly income (Applicant) = Basic Monthly Salary + Allowance commission - Deduction
                                    $applicants_net_monthly_income = (double)$get('applicants_basic_monthly_salary') 
                                                                    + (double)$get('applicants_allowance_commission') - 
                                                                    (double)$get('applicants_deductions');

                                    //Net Monthly income (Spouse) = Basic Monthly Salary (Spouse) + Allowance commission (Spouse) - Deduction (Spouse)
                                    $spouses_net_monthly_income = (double)$get('spouses_basic_monthly_salary') 
                                                                + (double)$get('spouse_allowance_commision') 
                                                                - (double)$get('spouse_deductions');

                                    $other_income = (float)$get('other_income');

                                    $gross_monthly_income = $applicants_net_monthly_income + $spouses_net_monthly_income + $other_income;

                                    $set('spouse_net_monthly_income', $spouses_net_monthly_income);
                                    $set('applicants_net_monthly_income', $applicants_net_monthly_income);
                                    $set('gross_monthly_income', $gross_monthly_income);

                    }),
                        ])
                        ->alignment(Alignment::Center)
                        ->columnSpan(1),
        ]);
    }

    public static function getExpenses(): Forms\Components\Component
    {
        return Forms\Components\Group::make([
                Forms\Components\Section::make("Expenses")
                ->columns(2)
                ->schema([
                        Forms\Components\TextInput::make("living_expenses")
                                ->label("Living Expenses:")
                                ->numeric()
                                ->default(0)
                                ->columnSpan(1),
                        Forms\Components\TextInput::make("education")
                                ->label("Education:")
                                ->numeric()
                                ->default(0)
                                ->columnSpan(1),
                        Forms\Components\TextInput::make("transportation")
                                ->label("Transportation:")
                                ->numeric()
                                ->numeric()
                                ->default(0)
                                ->columnSpan(1),
                        Forms\Components\TextInput::make("rental")
                                ->label("Rental:")
                                ->numeric()
                                ->numeric()
                                ->default(0)
                                ->columnSpan(1),
                        Forms\Components\TextInput::make("utilities")->label("Utilities:")->numeric()
                                ->numeric()
                                ->default(0)
                                ->columnSpan(1),
                        Forms\Components\TextInput::make("monthly_amortization")->label("Monthly Amortization:")->numeric()
                                ->numeric()
                                ->default(0)
                                ->columnSpan(1),
                        Forms\Components\TextInput::make("other_expenses")->label("Other Expenses:")->numeric()
                                ->numeric()
                                ->default(0)
                                ->columnSpan(1),
                        Forms\Components\TextInput::make("total_expenses")->label("Total Expenses:")->numeric()->columnSpan(1)
                                ->numeric()
                                ->required()
                                ->default(0)
                                ->prefix('Total:')
                                ->columnSpan(1),
                        Forms\Components\Actions::make([
                                Forms\Components\Actions\Action::make('Calculate Expenses')
                                ->icon('heroicon-m-calculator')
                                ->action(function (Forms\Set $set, Forms\Get $get){
        
                                    $total_expenses = (double)$get('living_expenses') + (double)$get('education') 
                                                    + (double)$get('transportation') + (double)$get('rental')
                                                    + (double)$get('utilities') + (double)$get('monthly_amortization')
                                                    + (double)$get('other_expenses');
        
                                    $set('total_expenses', $total_expenses);
        
                                }),
                        ])->columnSpan(2),
                ]),
        ]);
    }

    public static function getNetIncome(): Forms\Components\Component
    {
        return Forms\Components\Group::make([
            Forms\Components\Section::make("Net Monthly Income:")
            ->schema([
                    Forms\Components\TextInput::make("net_monthly_income")->label("Net Monthly Income:")
                            ->numeric()
                            ->required()
                            ->prefix('Total:'),

                    Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('Calculate Net Monthly Income')
                            ->icon('heroicon-m-calculator')
                            ->action(
                                    function (Forms\Set $set, Forms\Get $get){
                                            $net_monthly_income = (double)$get('gross_monthly_income') - (double)$get('total_expenses');
                                            $set('net_monthly_income', $net_monthly_income);
        
                            }),
                        ])->alignment(Alignment::Center),

            ]),
        ]);
    }

    public static function getImageStatementMonthlyIncome(): Forms\Components\Component
    {
        return Forms\Components\Group::make([
                Forms\Components\Fileupload::make('proof_of_income_image')
                ->disk('public')
                ->multiple(true)
                ->minFiles(2)
                ->maxFiles(2)
                ->label('Proof of income:')
                ->hint("Requires two (2) images of Proof of Income.")
                ->required(true)
                ->columnSpan(3),
        ]);
    }

    public static function getStatementOfMonthlyIncome(): Forms\Components\Component
    {
        return Forms\Components\Group::make([
            CustomerApplicationResource::getIncome()->columnSpan(2),
            CustomerApplicationResource::getExpenses()->columnSpan(1),
            CustomerApplicationResource::getNetIncome()->columnSpan(3),
            CustomerApplicationResource::getImageStatementMonthlyIncome()->columnSpan(3)
        ])
        ->columns(3);
    }

    public static function getResubmissionNotes(): Forms\Components\Component
    {
        return Forms\Components\Group::make([
                Forms\Components\TextArea::make('resubmission_note')
                        ->label('Rejection Note')
                        ->disabled(true),
        ]);
    }

    public static function getRejectionNotes(): Forms\Components\Component
    {
        return Forms\Components\Group::make([
                Forms\Components\TextArea::make('reject_note')
                        ->label('Rejection Note'),
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
                Forms\Components\Placeholder::make('Branch')
                	->content(Models\Branch::query()->where("id", auth()->user()->branch_id)->first()->full_address),
                Forms\Components\Wizard::make([
                        Forms\Components\Wizard\Step::make('Unit')
                                ->schema([
                                        CustomerApplicationResource::getUnitToBeFinanced()
                                                ->disabled(
                                                        function(string $operation){
                                                            if($operation == "edit"){
                                                                return true;
                                                }}),
                                ]),
                        Forms\Components\Wizard\Step::make('Applicant Information')
                                ->schema([
                                        CustomerApplicationResource::getApplicantInformation(),
                                        CustomerApplicationResource::getSpouseComponents(),
                                        CustomerApplicationResource::getCoOwnerInformation()
                                ]),
                        Forms\Components\Wizard\Step::make('Educational Attainment')
                                ->schema([
                                        CustomerApplicationResource::getEducationalAttainment()
                                ]),
                        Forms\Components\Wizard\Step::make('References')
                                ->schema([
                                        CustomerApplicationResource::getReferences()
                                ]),
                        Forms\Components\Wizard\Step::make('Employment')
                                ->schema([
                                        CustomerApplicationResource::getEmployment()
                                ]),
                        Forms\Components\Wizard\Step::make('Statement of Month. income')
                                ->schema([
                                        CustomerApplicationResource::getProperties(),
                                        CustomerApplicationResource::getStatementOfMonthlyIncome(),
                                ]),
                ])
                ->columnSpan(6)
                ->submitAction(new HtmlString(Blade::render(<<<BLADE
                <x-filament::button
                    type="submit"
                    size="sm"
                >
                    Submit
                </x-filament::button>
            BLADE))),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfoLists\Components\Tabs::make('Label')
                            ->tabs([
                                InfoLists\Components\Tabs\Tab::make("Application's Information")
                                    ->schema([
                                            InfoLists\Components\Section::make("Application's Information")
                                                ->schema([
                                                    InfoLists\Components\TextEntry::make('application_status')
                                                            ->label("Application's status")
                                                            ->badge(),
                                                    InfoLists\Components\TextEntry::make('release_status')
                                                            ->label("Relase status")
                                                            ->badge(),
                                                    InfoLists\Components\TextEntry::make('created_at')
                                                            ->dateTime('M d Y')
                                                            ->label('Date Created')
                                                            ->badge(),
                                                    InfoLists\Components\TextEntry::make('preffered_unit_status')
                                                            ->label("Preffered unit status"),
                                                    InfoLists\Components\TextEntry::make('due_date')
                                                            ->label('Upcoming Due')
                                                            ->badge()
                                                            ->color('danger'),
                                            ])
                                            ->columns(6),
                                            InfoLists\Components\Section::make("Branch Information")
                                                ->schema([
                                                    InfoLists\Components\TextEntry::make('branches.full_address')
                                                            ->size(TextEntry\TextEntrySize::Small)
                                                            ->columnSpan(6)
                                                            ->label("Branch"),
                                                    InfoLists\Components\TextEntry::make('contact')
                                                            ->size(TextEntry\TextEntrySize::Small)
                                                            ->columnSpan(6)
                                                            ->label("Contact No."),
                                            ]),
                                    ])->columns(6),
                                InfoLists\Components\Tabs\Tab::make('Unit Information')
                                    ->schema([
                                            InfoLists\Components\Section::make("Motorcycle's Image")
                                                    ->schema([
                                                            InfoLists\Components\ImageEntry::make('unitModel.image_file')
                                                                    ->label("")
                                                                    ->disk('public')
                                                                    ->height(200)
                                                                    ->width(200),
                                                    ])->columnSpan(2),
                                            InfoLists\Components\Section::make("Motorcycle's Information")
                                                    ->schema([
                                                        InfoLists\Components\TextEntry::make('unitModel.model_name')
                                                            ->label('Unit Model'),
                                                        InfoLists\Components\TextEntry::make('units.chasis_number')
                                                                ->label('Chasis number')
                                                                ->badge(),   
                                                        InfoLists\Components\TextEntry::make('unit_term')
                                                                ->label('Unit Term'),
                                                        InfoLists\Components\TextEntry::make('unit_ttl_dp')
                                                                ->label('Down Payment')
                                                                ->money('php'),   
                                                        InfoLists\Components\TextEntry::make('unit_monthly_amort_fin')
                                                                ->label('Monthly Amortization')
                                                                ->money('php'),                     
                                                        InfoLists\Components\TextEntry::make('unit_srp')
                                                                ->label('Unit Price')
                                                                ->money('php'),
                                                    ])->columnSpan(4),
                                    ])
                                    ->columns(6),
                                InfoLists\Components\Tabs\Tab::make("Customer's Information")
                                    ->schema([
                                            InfoLists\Components\Section::make([
                                                    InfoLists\Components\TextEntry::make('applicant_firstname')->label('First Name')->columnSpan(2),
                                                    InfoLists\Components\TextEntry::make('applicant_lastname')->label('Last Name')->columnSpan(2),
                                                    InfoLists\Components\TextEntry::make('applicant_birthday')->label('Birthday')->columnSpan(2),
                                            ])
                                            ->columns(6)
                                            ->columnSpan(3),
                                            InfoLists\Components\Section::make([
                                                    InfoLists\Components\TextEntry::make('applicant_telephone')->label('Contact Number:')
                                                            ->columnSpan(3),
                                            ])
                                            ->columns(6)
                                            ->columnSpan(3),

                                            InfoLists\Components\Section::make([
                                                    InfoLists\Components\TextEntry::make('applicant_house')
                                                            ->label('House:')
                                                            ->columnSpan(3),
                                                    InfoLists\Components\TextEntry::make('applicant_present_address')
                                                            ->label('Present Address:')
                                                            ->columnSpan(3),
                                            ])
                                            ->columns(6)
                                            ->columnSpan(6),
                                            InfoLists\Components\Section::make([
                                                InfoLists\Components\ImageEntry::make('applicant_valid_id')
                                                            ->columnSpan(6)
                                                            ->disk('public')
                                                            ->width(400)
                                                            ->height(400)
                                                            ->label("Provided ID(s)"),
                                        ]),
                                    ]),
                                InfoLists\Components\Tabs\Tab::make("Co-maker's Information")
                                    ->schema([
											InfoLists\Components\TextEntry::make('co_owner_firstname')
													->label('First Name:')
													->columnSpan(3),
											InfoLists\Components\TextEntry::make('co_owner_middlename')
													->label('Middle Name:')
													->columnSpan(3),
											InfoLists\Components\TextEntry::make('co_owner_lastname')
													->label('Last Name:')
													->columnSpan(3),
											InfoLists\Components\TextEntry::make('co_owner_mobile_number')
													->label('Contact No.:')
													->columnSpan(3),
											InfoLists\Components\TextEntry::make('co_owner_address')
													->label('Address:')
													->columnSpan(3),
											InfoLists\Components\ImageEntry::make('co_owner_valid_id')
													->label("Valid ID's:")
                                                                                                        ->width(400)
                                                                                                        ->height(400)
													->columnSpan(3),
                                    ]),
                                InfoLists\Components\Tabs\Tab::make('Statement of Monthly Income')
                                    ->schema([
											InfoLists\Components\TextEntry::make('net_monthly_income')
													->label('Net Income:')
													->color('success')
													->money('PHP')
													->columnSpan(3),
											InfoLists\Components\ImageEntry::make('proof_of_income_image')
                                                                                                        ->disk('public')
													->label('Proof of income:')
                                                                                                        ->width(500)
                                                                                                        ->height(500)
													->columnSpan(3),
                                    ]),
                    ])
                    ->columns(6)
                    ->columnSpan(6),

            ])->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                        ->label("Application ID:")
                        ->searchable(),
                Tables\Columns\TextColumn::make('application_type')
                        ->label("Type:")
                        ->badge()
                        ->searchable(),
                Tables\Columns\TextColumn::make('application_status')
                        ->label("Status:")
                        ->badge(),
                Tables\Columns\TextColumn::make('units.engine_number')
                        ->label("Engine No.")
                        ->badge(),
                Tables\Columns\TextColumn::make('applicant_firstname')
                        ->label("First Name:")
                        ->searchable(),
                Tables\Columns\TextColumn::make('applicant_lastname')
                        ->label("Last Name:")
                        ->searchable(),
                Tables\Columns\TextColumn::make('unitModel.model_name')
                        ->label("Unit Model:"),
                Tables\Columns\TextColumn::make('created_at')
                        ->label("Date Created:")
                        ->dateTime('d-M-Y'),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false)
            ->filters([
                Tables\Filters\SelectFilter::make('application_status')
                        ->options([
                            'pending' => 'Pending',
                            'active' => 'Active',
                            'reject' => 'Reject'
                        ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListCustomerApplications::route('/'),
            'create' => Pages\CreateCustomerApplication::route('/create'),
            'view' => Pages\ViewCustomerApplication::route('/{record}'),
            'edit' => Pages\EditCustomerApplication::route('/{record}/edit'),
        ];
    }    
}
