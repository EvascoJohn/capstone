<?php

namespace App\Filament\TestPanel\Resources;

use App\Filament\TestPanel\Resources\CustomerApplicationResource\Pages;
use App\Filament\TestPanel\Resources\CustomerApplicationResource\RelationManagers;
use App\Models\ComponentHelper\ResubmissionHelper;
use App\Models\CustomerApplication;
use Filament\Forms;
use Filament;
use App\Models;
use App\Enums;
use App\Models\DealerhipCalculations;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Support\Enums\Alignment;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use App\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Filament\Actions\CreateAction;
use Filament\Support\Enums\FontWeight;
use Filament\Infolists\Components\TextEntry;
use Livewire;

class CustomerApplicationResource extends Resource
{
    protected static ?string $model = CustomerApplication::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Select::make("branch_id")
                    ->relationship('branches', 'full_address')
                    ->searchable('full_address')
                    ->columnspan(6)
                    ->required()
                    ->preload(),            
            // This wizard is for resubmission.
            Forms\Components\Wizard::make()
            ->schema([
                Forms\Components\Wizard\Step::make('Resubmit Unit')
                        ->hidden(function(?Model $record){
                            if($record != null){
                                if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                    return true;
                                }
                            }
                            return true;
                        })
                        ->schema([
                                CustomerApplicationResource::getUnitToBeFinanced()
                                        ->disabled(
                                                function(string $operation){
                                                    if($operation == "edit"){
                                                        return true;
                                        }}),
                        ]),
                Forms\Components\Wizard\Step::make('Resubmit Applicant Information')
                        ->hidden(function(?Model $record){
                                $helper = new Models\ComponentHelpers\ResubmissionHelper();
                                return  $helper->showSectionIfExist($record, Enums\ApplicationSections::APPLICANT->value);
                        })
                        ->schema([
                                Forms\Components\Placeholder::make('applicant_section_note')
                                        ->columnSpan(1)
                                        ->label('Note')
                                        ->content(function(?Model $record):string{
                                            $helper = new Models\ComponentHelpers\ResubmissionHelper();
                                            return $helper->getSectionNote($record, Enums\ApplicationSections::APPLICANT->value, 'applicant_section_note');
                                        })
                                        ->hidden(function(?Model $record): bool {
                                                $helper = new Models\ComponentHelpers\ResubmissionHelper();
                                                return  $helper->showSectionIfExist($record, Enums\ApplicationSections::APPLICANT->value);
                                        }),
                                CustomerApplicationResource::getApplicantInformation(),
                                CustomerApplicationResource::getSpouseComponents(),
                                CustomerApplicationResource::getCoOwnerInformation()
                        ]),
                Forms\Components\Wizard\Step::make('Educational Attainment')
                        ->hidden(function(?Model $record){
                                $helper = new Models\ComponentHelpers\ResubmissionHelper();
                                return  $helper->showSectionIfExist($record, Enums\ApplicationSections::EDUCATION->value);
                        })
                        ->schema([
                                Forms\Components\Placeholder::make('education_section_note')
                                        ->columnSpan(1)
                                        ->label('Note')
                                        ->content(function(?Model $record):string{
                                                $helper = new Models\ComponentHelpers\ResubmissionHelper();
                                                return $helper->getSectionNote($record, Enums\ApplicationSections::EDUCATION->value, 'education_section_note');
                                        })
                                        ->hidden(function(?Model $record): bool {
                                                $helper = new Models\ComponentHelpers\ResubmissionHelper();
                                                return  $helper->showSectionIfExist($record, Enums\ApplicationSections::EDUCATION->value);
                                        }),
                                    CustomerApplicationResource::getEducationalAttainment()
                        ]),
                Forms\Components\Wizard\Step::make('References')
                        ->hidden(function(?Model $record){
                                $helper = new Models\ComponentHelpers\ResubmissionHelper();
                                return  $helper->showSectionIfExist($record, Enums\ApplicationSections::REFERENCES->value);
                        })
                        ->schema([
                                Forms\Components\Placeholder::make('references_section_note')
                                        ->columnSpan(1)
                                        ->label('Note')
                                        ->content(function(?Model $record):string{
                                                $helper = new Models\ComponentHelpers\ResubmissionHelper();
                                                return $helper->getSectionNote($record, Enums\ApplicationSections::REFERENCES->value, 'references_section_note');
                                        })
                                        ->hidden(function(?Model $record): bool {
                                                $helper = new Models\ComponentHelpers\ResubmissionHelper();
                                                return  $helper->showSectionIfExist($record, Enums\ApplicationSections::REFERENCES->value);
                                        }),
                                CustomerApplicationResource::getReferences()
                        ]),
                Forms\Components\Wizard\Step::make('Employment')
                        ->hidden(function(?Model $record){
                                $helper = new Models\ComponentHelpers\ResubmissionHelper();
                                return  $helper->showSectionIfExist($record, Enums\ApplicationSections::EMPLOYMENT->value);
                        })
                        ->schema([
                                Forms\Components\Placeholder::make('references_section_note')
                                        ->columnSpan(1)
                                        ->label('Note')
                                        ->content(function(?Model $record):string{
                                                $helper = new Models\ComponentHelpers\ResubmissionHelper();
                                                return $helper->getSectionNote($record, Enums\ApplicationSections::EMPLOYMENT->value, 'employment_section_note');
                                        })
                                        ->hidden(function(?Model $record): bool {
                                                $helper = new Models\ComponentHelpers\ResubmissionHelper();
                                                return  $helper->showSectionIfExist($record, Enums\ApplicationSections::EMPLOYMENT->value);
                                        }),
                                CustomerApplicationResource::getEmployment()
                        ]),
                Forms\Components\Wizard\Step::make('Statement of Month. income')
                        ->hidden(function(?Model $record){
                                $helper = new Models\ComponentHelpers\ResubmissionHelper();
                                return  $helper->showSectionIfExist($record, Enums\ApplicationSections::MONTHLY_INCOME->value);
                        })
                        ->schema([
                                Forms\Components\Placeholder::make('monthy_income_section_note')
                                        ->columnSpan(1)
                                        ->label('Note')
                                        ->content(function(?Model $record):string{
                                                $helper = new Models\ComponentHelpers\ResubmissionHelper();
                                                return $helper->getSectionNote($record, Enums\ApplicationSections::MONTHLY_INCOME->value, 'monthy_income_section_note');
                                        })
                                        ->hidden(function(?Model $record): bool {
                                                $helper = new Models\ComponentHelpers\ResubmissionHelper();
                                                return  $helper->showSectionIfExist($record, Enums\ApplicationSections::MONTHLY_INCOME->value);
                                        }),
                                CustomerApplicationResource::getProperties(),
                                CustomerApplicationResource::getStatementOfMonthlyIncome(),
                        ]),
        ])
        ->columnSpan(6)
        ->hidden(function(string $operation){
            if($operation == 'edit'){
                return false;
            }
            else{
                return true;
            }
        })
        ->disabled(function(string $operation){
            if($operation == 'edit'){
                return false;
            }
            else{
                return true;
            }
        }),

            // This wizard is for fillups.
            Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Unit')
                            ->hidden(function(?Model $record){
                                if($record != null){
                                    if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                        return true;
                                    }
                                }
                                return false;
                            })
                            ->schema([
                                    CustomerApplicationResource::getUnitToBeFinanced()
                                            ->disabled(
                                                    function(string $operation){
                                                        if($operation == "edit"){
                                                            return true;
                                                    
                                                        }
                                                    return false;
                                        }),
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
            ->hidden(function(string $operation){
                if($operation == 'edit'){
                    return true;
                }
                else{
                    return false;
                }
            })
            ->disabled(function(string $operation){
                if($operation == 'edit'){
                    return true;
                }
                else{
                    return false;
                }
            })
            ->submitAction(
                new HtmlString(Blade::render(<<<BLADE
                <x-filament::button
                    type="submit"
                    size="sm"
                >
                    Submit
                </x-filament::button>
            BLADE))),            
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
                Tables\Columns\TextColumn::make('id')
                ->label("Application ID:")
                ->searchable(),
        Tables\Columns\TextColumn::make('application_type')
                ->label("Status:")
                ->badge(),                 
        Tables\Columns\TextColumn::make('application_status')
                ->label("Status:")
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
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                        ->label('Update')
                        ->hidden(function(?Model $record): bool {
                                if($record->application_status == ApplicationStatus::RESUBMISSION_STATUS){
                                        return false;
                                }
                                return true;
                        }
                ),
            ])
            ->bulkActions([

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
                                    InfoLists\Components\Tabs\Tab::make("Application's Information")
                                            ->columns(6)
                                            ->schema([
                                                    InfoLists\Components\Section::make("Application's Information")
                                                            ->columns(12)
                                                            ->schema([
                                                                    InfoLists\Components\TextEntry::make('application_status')
                                                                            ->columnSpan(2)
                                                                            ->label("Application's status")
                                                                            ->badge(),
                                                                    InfoLists\Components\TextEntry::make('plan')
                                                                            ->columnSpan(2)
                                                                            ->label("Plan Type")
                                                                            ->badge(),
                                                                    InfoLists\Components\TextEntry::make('release_status')
                                                                            ->columnSpan(2)
                                                                            ->label("Relase status")
                                                                            ->badge(),
                                                                    InfoLists\Components\TextEntry::make('created_at')
                                                                            ->columnSpan(2)
                                                                            ->dateTime('M d Y')
                                                                            ->label('Date Created')
                                                                            ->badge(),
                                                                    InfoLists\Components\TextEntry::make('preffered_unit_status')
                                                                            ->badge()
                                                                            ->columnSpan(2)
                                                                            ->label("Preffered unit status"),
                                                                    InfoLists\Components\TextEntry::make('due_date')
                                                                            ->columnSpan(2)
                                                                            ->label('Upcoming Due')
                                                                            ->badge()
                                                                            ->color('danger'),
                                                            ]),
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
                                            ]),
                                    InfoLists\Components\Tabs\Tab::make('Unit Information')
                                            ->columns(6)
                                            ->schema([
                                                    InfoLists\Components\Section::make("Motorcycle's Image")
                                                            ->columnSpan(2)
                                                            ->schema([
                                                                    InfoLists\Components\ImageEntry::make('unitModel.image_file')
                                                                            ->label("")
                                                                            ->disk('public')
                                                                            ->height(200)
                                                                            ->width(200),
                                                            ]),
                                                    InfoLists\Components\Section::make("Motorcycle's Information")
                                                            ->columns(6)
                                                            ->columnSpan(4)
                                                            ->schema([
                                                                    InfoLists\Components\TextEntry::make('unitModel.model_name')
                                                                            ->columnSpan(2)
                                                                            ->label('Unit Model'),
                                                                    InfoLists\Components\TextEntry::make('units.chasis_number')
                                                                            ->columnSpan(2)
                                                                            ->label('Chasis number')
                                                                            ->badge(),   
                                                                    InfoLists\Components\TextEntry::make('unit_term')
                                                                            ->columnSpan(2)
                                                                            ->label('Unit Term'),
                                                                    InfoLists\Components\TextEntry::make('unit_ttl_dp')	
                                                                            ->columnSpan(2)
                                                                            ->label('Down Payment')
                                                                            ->money('php'),   
                                                                    InfoLists\Components\TextEntry::make('unit_monthly_amort_fin')
                                                                            ->columnSpan(2)
                                                                            ->label('Monthly Payment')
                                                                            ->money('php'),                     
                                                                    InfoLists\Components\TextEntry::make('unit_srp')
                                                                            ->columnSpan(2)
                                                                            ->label('Unit Price')
                                                                            ->money('php'),
                                                                    InfoLists\Components\TextEntry::make('preffered_unit_status')
                                                                            ->columnSpan(2)
                                                                            ->label('Status'),
                                                            ]),
                                            ]),
                                    InfoLists\Components\Tabs\Tab::make("Customer's Information")
                                            ->schema([
                                                    InfoLists\Components\Section::make("Personal Information")
                                                            ->columns(6)
                                                            ->columnSpan(3)
                                                            ->schema([
                                                                InfoLists\Components\TextEntry::make('applicant_firstname')
                                                                        ->label('First Name')
                                                                        ->columnSpan(2),
                                                                InfoLists\Components\TextEntry::make('applicant_lastname')
                                                                        ->label('Last Name')
                                                                        ->columnSpan(2),
                                                                InfoLists\Components\TextEntry::make('applicant_birthday')
                                                                        ->label('Birthday')
                                                                        ->columnSpan(2),
                                                            ]),
                                                    InfoLists\Components\Section::make("Contact Information")
                                                            ->columns(6)
                                                            ->columnSpan(3)
                                                            ->schema([
                                                                    InfoLists\Components\TextEntry::make('applicant_telephone')
                                                                            ->label('Contact Number:')
                                                                            ->columnSpan(3),
                                                                    InfoLists\Components\TextEntry::make('applicant_email')->label('Email:')
                                                                            ->columnSpan(3),
                                                            ]),
                                                    InfoLists\Components\Section::make("Location Information")
                                                            ->columns(6)
                                                            ->columnSpan(6)
                                                            ->schema([
                                                                    InfoLists\Components\TextEntry::make('applicant_house')
                                                                            ->label('House:')
                                                                            ->columnSpan(3),
                                                                    InfoLists\Components\TextEntry::make('applicant_present_address')
                                                                            ->label('Present Address:')
                                                                            ->columnSpan(3),
                                                            ]),
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
                                                    InfoLists\Components\Section::make("Personal Information")
                                                            ->columns(6)
                                                            ->columnSpan(3)
                                                            ->schema([
                                                                InfoLists\Components\TextEntry::make('co_owner_firstname')
                                                                                ->label('First Name')
                                                                                ->columnSpan(2),
                                                                InfoLists\Components\TextEntry::make('co_owner_middlename')
                                                                                ->label('Middle Name')
                                                                                ->columnSpan(2),
                                                                InfoLists\Components\TextEntry::make('co_owner_lastname')
                                                                                ->label('Last Name')
                                                                                ->columnSpan(2),
                                                            ]),
                                                    InfoLists\Components\Section::make("Contact Information")
                                                            ->columns(6)
                                                            ->columnSpan(3)
                                                            ->schema([
                                                                    InfoLists\Components\TextEntry::make('co_owner_mobile_number')
                                                                            ->label('Contact Information')
                                                                            ->columnSpan(3),
                                                                    InfoLists\Components\TextEntry::make('co_owner_email')
                                                                            ->label('Email')
                                                                            ->columnSpan(3),
                                                            ]),
                                                    InfoLists\Components\Section::make("Location Information")
                                                            ->columns(6)
                                                            ->columnSpan(6)
                                                            ->schema([
                                                                    InfoLists\Components\TextEntry::make('co_owner_address')
                                                                            ->label('Address:')
                                                                            ->columnSpan(3),
                                                            ]),
                                                    InfoLists\Components\ImageEntry::make('co_owner_valid_id')
                                                                    ->label("Valid ID's:")
                                                                    ->width(400)
                                                                    ->height(400)
                                                                    ->columnSpan(6),
                                            ]),
                                    InfoLists\Components\Tabs\Tab::make('Statement of Monthly Income')
                                            ->columns(8)
                                            ->schema([
                                                    InfoLists\Components\Section::make("Applicant's Net Income")
                                                            ->columnSpan(4)
                                                            ->description("The applicant's net monthly income.")
                                                            ->columns(12)
                                                            ->schema([
                                                                InfoLists\Components\TextEntry::make('applicants_basic_monthly_salary')
                                                                        ->label("Basic Monthly Salary")
                                                                        ->color('success')
                                                                        ->money('PHP')
                                                                        ->columnSpan(4),
                                                                InfoLists\Components\TextEntry::make('applicants_allowance_commission')
                                                                        ->label("Allowance Commision")
                                                                        ->color('success')
                                                                        ->money('PHP')
                                                                        ->columnSpan(4),
                                                                InfoLists\Components\TextEntry::make('applicants_deductions')
                                                                        ->label("Deductions")
                                                                        ->color('danger')
                                                                        ->money('PHP')
                                                                        ->columnSpan(4),
                                                                InfoLists\Components\TextEntry::make('applicants_net_monthly_income')
                                                                        ->label(" Net Monthly Income")
                                                                        ->color('success')
                                                                        ->money('PHP')
                                                                        ->columnSpan(12),
                                                            ]),
                                                    InfoLists\Components\Section::make("Spouse's Net Income")
                                                            ->columnSpan(4)
                                                            ->description("The spouse's net monthly income.")
                                                            ->columns(12)
                                                            ->schema([
                                                                    InfoLists\Components\TextEntry::make('spouses_basic_monthly_salary')
                                                                            ->label('Basic Monthly Salary')
                                                                            ->color('success')
                                                                            ->money('PHP')
                                                                            ->columnSpan(4),
                                                                    InfoLists\Components\TextEntry::make('spouse_allowance_commision')
                                                                            ->label("Allowance Commision")
                                                                            ->color('success')
                                                                            ->money('PHP')
                                                                            ->columnSpan(4),
                                                                    InfoLists\Components\TextEntry::make('spouse_deductions')
                                                                            ->label("Deduction")
                                                                            ->color('danger')
                                                                            ->money('PHP')
                                                                            ->columnSpan(4),
                                                                    InfoLists\Components\TextEntry::make('spouse_net_monthly_income')
                                                                            ->label("Net Monthly Income")
                                                                            ->color('success')
                                                                            ->money('PHP')
                                                                            ->columnSpan(12),
                                                            ]),
                                                    InfoLists\Components\Section::make("Expenses")
                                                            ->columnSpan(8)
                                                            ->description("These are expenses.")
                                                            ->columns(8)
                                                            ->schema([
                                                                    InfoLists\Components\TextEntry::make('living_expenses')
                                                                            ->label("Living Expenses")
                                                                            ->color('danger')
                                                                            ->money('PHP')
                                                                            ->columnSpan(2),
                                                                    InfoLists\Components\TextEntry::make('education')
                                                                            ->label("Education")
                                                                            ->color('danger')
                                                                            ->money('PHP')
                                                                            ->columnSpan(2),
                                                                    InfoLists\Components\TextEntry::make('transportation')
                                                                            ->label("Transportation")
                                                                            ->color('danger')
                                                                            ->money('PHP')
                                                                            ->columnSpan(2),
                                                                    InfoLists\Components\TextEntry::make('rental')
                                                                            ->label("Rentals")
                                                                            ->color('danger')
                                                                            ->money('PHP')
                                                                            ->columnSpan(2),
                                                                    InfoLists\Components\TextEntry::make('utilities')
                                                                            ->label("Utilities")
                                                                            ->color('danger')
                                                                            ->money('PHP')
                                                                            ->columnSpan(2),
                                                                    InfoLists\Components\TextEntry::make('unit_monthly_amort_fin')
                                                                            ->label("Monthly Payment")
                                                                            ->color('danger')
                                                                            ->money('PHP')
                                                                            ->columnSpan(2),
                                                                    InfoLists\Components\TextEntry::make('total_expenses')
                                                                            ->label("Total Expenses")
                                                                            ->color('danger')
                                                                            ->money('PHP')
                                                                            ->columnSpan(2),
                                                            ]),
                                                    Infolists\Components\Section::make("")
                                                            ->columns(12)
                                                            ->columnSpan(8)
                                                            ->schema([
                                                                    InfoLists\Components\TextEntry::make('other_income')
                                                                            ->label("Other Income")
                                                                            ->color('success')
                                                                            ->money('PHP')
                                                                            ->columnSpan(4),
                                                                    InfoLists\Components\TextEntry::make('gross_monthly_income')
                                                                            ->label("Gross Monthly Income")
                                                                            ->color('success')
                                                                            ->money('PHP')
                                                                            ->columnSpan(4),
                                                                    InfoLists\Components\TextEntry::make('net_monthly_income')
                                                                            ->label("Net Monthly Income")
                                                                            ->color('success')
                                                                            ->money('PHP')
                                                                            ->columnSpan(4),
                                                            ]),
                                                InfoLists\Components\Section::make("Applicant's Net Income")
                                                                ->columnSpan(8)
                                                                ->description("The applicant's net monthly income.")
                                                                ->columns(12)
                                                                ->schema([
                                                                        InfoLists\Components\TextEntry::make('number_of_vehicles')
                                                                                        ->label("Number of vehicles")
                                                                                        ->columnSpan(12),
                                                                        InfoLists\Components\RepeatableEntry::make('real_estate_property')
                                                                                        ->label("Real Estate(s)")
                                                                                        ->columnSpan(12)
                                                                                        ->columns(12)
                                                                                        ->schema([
                                                                                                        InfoLists\Components\TextEntry::make('type')
                                                                                                                        ->columnSpan(12)
                                                                                                                        ->badge(),
                                                                                                        InfoLists\Components\TextEntry::make('clean')
                                                                                                                        ->columnSpan(3),
                                                                                                        InfoLists\Components\TextEntry::make('mortgage')
                                                                                                                        ->columnSpan(3)
                                                                                                                        ->money('PHP'),
                                                                                                        InfoLists\Components\TextEntry::make('to_whom')
                                ->columnSpan(3),
                                                                                                        InfoLists\Components\TextEntry::make('market_value')
                                                                                                                        ->columnSpan(3)
                                                                                                                        ->money('PHP'),
                                                                                        ]),
                                                                        InfoLists\Components\RepeatableEntry::make('appliance_property')
                                                                                        ->label("Appliance(s)")
                                                                                        ->columnSpan(6)
                                                                                        ->schema([
                                                                                                        InfoLists\Components\TextEntry::make('name'),
                                                                                        ]),
                                                                ]),
                                                    InfoLists\Components\ImageEntry::make('proof_of_income_image')
                                                            ->disk('public')
                                                            ->label('Proof of income:')
                                                            ->width(500)
                                                            ->height(500)
                                                            ->columnSpan(6),
                                            ]),
                                    InfoLists\Components\Tabs\Tab::make('Financial References')
                                            ->columns(8)
                                            ->schema([
                                                    InfoLists\Components\Section::make("Applicant's Personal Reference(s)")
                                                            ->columnSpan(8)
                                                            ->description("The applicant's personal reference(s) (This field is required)")
                                                            ->columns(12)
                                                            ->schema([
                                                                    InfoLists\Components\RepeatableEntry::make("personal_references")
                                                                            ->label("Applicant's Personal Reference(s)")
                                                                            ->columnSpan(12)
                                                                            ->columns(12)
                                                                            ->schema([
                                                                                    InfoLists\Components\TextEntry::make('name')
                                                                                            ->label("Name")
                                                                                            ->columnSpan(3),
                                                                                    InfoLists\Components\TextEntry::make('address')
                                                                                            ->label("Address")
                                                                                            ->columnSpan(3),
                                                                                    InfoLists\Components\TextEntry::make('relationship')
                                                                                            ->label("Relationship")
                                                                                            ->columnSpan(3),
                                                                                    InfoLists\Components\TextEntry::make('telephone')
                                                                                            ->label("Contact Information")
                                                                                            ->columnSpan(3),
                                                                            ]),
                                                            ]),
                                                    InfoLists\Components\Section::make("Applicant's Credit Card Information")
                                                            ->columnSpan(8)
                                                            ->description("The applicant's Credit Card Information (This field is not required and may be empty)")
                                                            ->columns(12)
                                                            ->schema([
                                                                    InfoLists\Components\RepeatableEntry::make('applicants_card_information')
                                                                            ->columns(12)
                                                                            ->columnSpan(12)
                                                                            ->schema([
                                                                                    InfoLists\Components\TextEntry::make('bank_acc_type')
                                                                                            ->label("Account Type")
                                                                                            ->badge()
                                                                                            ->columnSpan(3),
                                                                                    InfoLists\Components\TextEntry::make('account_number')
                                                                                            ->label("Account No.")
                                                                                            ->badge()
                                                                                            ->columnSpan(3),
                                                                                    InfoLists\Components\TextEntry::make('bank_or_branch')
                                                                                            ->label("Bank\\Branch")
                                                                                            ->columnSpan(3),
                                                                                    InfoLists\Components\TextEntry::make('date_openned')
                                                                                            ->label("Date Openned")
                                                                                            ->columnSpan(3),
                                                                                    InfoLists\Components\TextEntry::make('average_monthly_balance')
                                                                                            ->label("Avg. Monthly Balance")
                                                                                            ->money('PHP')
                                                                                            ->columnSpan(3),
                                                                            ])
                                                            ]),
                                                    InfoLists\Components\Section::make("Creditor's Credit Card Information")
                                                            ->columnSpan(8)
                                                            ->description("This is the creditor's card reference (This field is not required and may be empty)")
                                                            ->columns(12)
                                                            ->schema([
                                                                    InfoLists\Components\RepeatableEntry::make('creditors_card_information')
                                                                            ->columns(12)
                                                                            ->columnSpan(12)
                                                                            ->schema([
                                                                                    InfoLists\Components\TextEntry::make('credit_card_company')
                                                                                            ->label("Card Company")
                                                                                            ->columnSpan(3),
                                                                                    InfoLists\Components\TextEntry::make('card_number')
                                                                                            ->label("Card No.")
                                                                                            ->badge()
                                                                                            ->columnSpan(3),
                                                                                    InfoLists\Components\TextEntry::make('card_date_issued')
                                                                                            ->label("Date Issued")
                                                                                            ->columnSpan(3),
                                                                                    InfoLists\Components\TextEntry::make('credit_limit')
                                                                                            ->label("Date Openned")
                                                                                            ->columnSpan(3),
                                                                                    InfoLists\Components\TextEntry::make('outstanding_balance')
                                                                                            ->label("Outstanding Balance")
                                                                                            ->money('PHP')
                                                                                            ->columnSpan(3),
                                                                            ]),
                                                            ]),
                                                    InfoLists\Components\Section::make("Creditor's Information")
                                                            ->columnSpan(8)
                                                            ->description("This is the creditor's information (This field is not required and may be empty)")
                                                            ->columns(12)
                                                            ->schema([
                                                                    Infolists\Components\RepeatableEntry::make('creditors_information')
                                                                            ->columnSpan(12)
                                                                            ->columns(12)
                                                                            ->schema([
                                                                                    InfoLists\Components\TextEntry::make('name')
                                                                                            ->label("Creditor Name")
                                                                                            ->columnSpan(3),
                                                                                    InfoLists\Components\TextEntry::make('term')
                                                                                            ->label("term")
                                                                                            ->columnSpan(3),
                                                                                    InfoLists\Components\TextEntry::make('present_balance')
                                                                                            ->label("Present Balance")
                                                                                            ->money('PHP')
                                                                                            ->columnSpan(3),
                                                                                    InfoLists\Components\TextEntry::make('principal')
                                                                                            ->label("Principal")
                                                                                            ->money('PHP')
                                                                                            ->columnSpan(3),
                                                                                    InfoLists\Components\TextEntry::make('monthly_amorthization')
                                                                                            ->label("Monthly Amortization")
                                                                                            ->money('PHP')
                                                                                            ->columnSpan(12),
                                                                            ]),
                                                            ]),
                                            ]),
                                    InfoLists\Components\Tabs\Tab::make('Educational Attainment')
                                            ->schema([
                                                    InfoLists\Components\Section::make("Applicant's educational Attainment")
                                                            ->columnSpan(8)
                                                            ->columns(12)
                                                            ->schema([
                                                                InfoLists\Components\RepeatableEntry::make('educational_attainment')
                                                                        ->columnSpan(12)
                                                                        ->columns(12)
                                                                        ->schema([
                                                                                InfoLists\Components\TextEntry::make('course')
                                                                                        ->label("Education")
                                                                                        ->columnSpan(3),
                                                                                InfoLists\Components\TextEntry::make('no_years')
                                                                                        ->label("Number of Years")
                                                                                        ->columnSpan(3),
                                                                                InfoLists\Components\TextEntry::make('school')
                                                                                        ->label("School")
                                                                                        ->columnSpan(3),
                                                                                InfoLists\Components\TextEntry::make('year_grad')
                                                                                        ->label("Year of graduate")
                                                                                        ->columnSpan(3),
                                                                        ])
                                                            ]),
                                    InfoLists\Components\Section::make("Dependent")
                                            ->columnSpan(8)
                                            ->columns(12)
                                            ->schema([
                                                    InfoLists\Components\RepeatableEntry::make('dependents')
                                                            ->columnSpan(12)
                                                            ->columns(12)
                                                            ->schema([
                                                                    InfoLists\Components\TextEntry::make('dependent_name')
                                                                            ->label("Name")
                                                                            ->columnSpan(3),
                                                                    InfoLists\Components\TextEntry::make('dependent_birthdate')
                                                                            ->label("Birthday")
                                                                            ->columnSpan(3),
                                                                    InfoLists\Components\TextEntry::make('dependent_school')
                                                                            ->label("School")
                                                                            ->columnSpan(3),
                                                                    InfoLists\Components\TextEntry::make('dependent_monthly_tuition')
                                                                            ->label("Monthly Tuition")
                                                                            ->columnSpan(3),
                                                        ])
                                            ]),
                                            ]),
                                    InfoLists\Components\Tabs\Tab::make('Employment')
                                            ->schema([
                                                    InfoLists\Components\Section::make("Applicant's Present Employer")
                                                            ->columnSpan(8)
                                                            ->columns(12)
                                                            ->schema([
                                                                    InfoLists\Components\TextEntry::make('applicant_present_business_employer')
                                                                            ->label("Employer")
                                                                            ->columnSpan(4),
                                                                    InfoLists\Components\TextEntry::make('applicant_position')
                                                                            ->label("Position")
                                                                            ->columnSpan(4),
                                                                    InfoLists\Components\TextEntry::make('applicant_how_long_job_or_business')
                                                                            ->label("School")
                                                                            ->columnSpan(4),
                                                            ]),
                                                    InfoLists\Components\Section::make("Applicant's Business")
                                                            ->description("The Applicant's Business (This field is not required and can be empty)")
                                                            ->columnSpan(8)
                                                            ->columns(12)
                                                            ->schema([
                                                                InfoLists\Components\TextEntry::make('applicant_business_address')
                                                                        ->label("Address")
                                                                        ->columnSpan(6),
                                                                InfoLists\Components\TextEntry::make('applicant_nature_of_business')
                                                                        ->label("Nature of Business")
                                                                        ->columnSpan(6),
                                                            ]),
                                                    InfoLists\Components\Section::make("Previous Employer")
                                                            ->description("The Applicant's previous employment (This field is not required and can be empty)")
                                                            ->columnSpan(8)
                                                            ->columns(12)
                                                            ->schema([
                                                                InfoLists\Components\TextEntry::make('applicant_previous_employer')
                                                                        ->label("Employer")
                                                                        ->columnSpan(6),
                                                                InfoLists\Components\TextEntry::make('applicant_previous_employer_position')
                                                                        ->label("Position")
                                                                        ->columnSpan(6),
                                                                InfoLists\Components\TextEntry::make('applicant_how_long_prev_job_or_business')
                                                                        ->label("How Long")
                                                                        ->columnSpan(6),
                                                            ]),
                                                    InfoLists\Components\Section::make("Spouse's Present Employment Information")
                                                            ->description("The Spouse's employment (This field is not required and can be empty)")
                                                            ->columnSpan(8)
                                                            ->columns(12)
                                                            ->schema([
                                                                InfoLists\Components\TextEntry::make('spouse_employer')
                                                                        ->label("Employer")
                                                                        ->columnSpan(6),
                                                                InfoLists\Components\TextEntry::make('spouse_position')
                                                                        ->label("Position")
                                                                        ->columnSpan(6),
                                                                InfoLists\Components\TextEntry::make('spouse_how_long_job_business')
                                                                        ->label("How Long")
                                                                        ->columnSpan(6),
                                                            ]),
                                                    InfoLists\Components\Section::make("Spouse's Present Business")
                                                            ->description("The Spouse's business (This field is not required and can be empty)")
                                                            ->columnSpan(8)
                                                            ->columns(12)
                                                            ->schema([
                                                                InfoLists\Components\TextEntry::make('spouse_business_address')
                                                                        ->label("Business Address")
                                                                        ->columnSpan(6),
                                                                InfoLists\Components\TextEntry::make('spouse_nature_of_business')
                                                                        ->label("Nature of Business")
                                                                        ->columnSpan(6),
                                                            ]),
                                            ]),
                        ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('author_id', auth()->id());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomerApplications::route('/'),
            'create' => Pages\CreateCustomerApplication::route('/create'),
            'edit' => Pages\EditCustomerApplication::route('/{record}/edit'),
            'view' => Pages\ViewCustomerApplicationResource::route('/{record}'),
        ];
    }

    public static function canEdit(Model $record): bool
    {
        return True;
    }

    public static function getUnitToBeFinanced(): Forms\Components\Component
    {
                return Forms\Components\Group::make()
                        ->columns(2)
                        ->schema([
                                Forms\Components\Fieldset::make("")
                                ->columnSpan(1)
                                ->columns(1)
                                ->schema([
                                        Forms\Components\Group::make()
                                                ->columns(6)
                                                ->columnSpan(1)
                                                ->schema([
                                                        Forms\Components\Select::make('unit_model_id')
                                                                ->columnSpan(4)
                                                                ->required()
                                                                ->label('Unit Model')
                                                                ->searchable(['unit_model_id'])
                                                                ->options(
                                                                        Models\Unit::getUnitsWithAvailableStock()
                                                                )
                                                                ->live()
                                                                ->afterStateUpdated(
                                                                                function(Forms\Get $get, Forms\Set $set)
                                                                                {
                                                                                        $unit_model = Models\UnitModel::find($get("unit_model_id"));
                                                                                        if($unit_model != null){
                                                                                                $set('unit_srp', $unit_model->price);
                                                                                        }
                                                                                }
                                                                ),
                                                        Forms\Components\TextInput::make('unit_srp')
                                                                        ->live()
                                                                        ->columnSpan(2)
                                                                        ->readOnly()
                                                                        ->required(true)
                                                                        ->label('Motorcycle Price:'),
                                                        Forms\Components\Select::make('preffered_unit_status')
                                                                        ->live()
                                                                        ->disabled(fn (Forms\Get $get): bool => ($get('unit_model_id') === null))
                                                                        ->columnSpan(2)
                                                                        ->required()
                                                                        ->label("Unit status")
                                                                        ->options(function (Forms\Get $get):array
                                                                        {
                                                                                return Models\Unit::getAvailableStatusOnUnit($get('unit_model_id'));
                                                                        }),
                                                        Forms\Components\Select::make('plan')
                                                                        ->live()
                                                                        ->disabled(fn (Forms\Get $get): bool => ($get('preffered_unit_status') === null))
                                                                        ->columnSpan(2)
                                                                        ->required()
                                                                        ->label("Plan")
                                                                        ->options(
                                                                                Enums\PlanStatus::class
									                                    ),
                                                        Forms\Components\Select::make('unit_term')
                                                                        ->columnSpan(2)
                                                                        ->live()
                                                                        ->disabled(fn (Forms\Get $get): bool => ($get('plan') != Enums\PlanStatus::INSTALLMENT->value))
                                                                        ->required()
                                                                        ->label("Term/Months")
                                                                        ->options(
                                                                                function():array
                                                                                {
                                                                                    $terms_and_amortizations = Models\CustomerApplicationMaintenance::first();
                                                                                    if($terms_and_amortizations != null){
                                                                                        return Models\DealerhipCalculations::extractKeyValuePairs($terms_and_amortizations->getAttributes()['monthly_amortizations']);
                                                                                    }
                                                                                    return [];
                                                                                }
                                                                        )
                                                                        ->afterStateUpdated(
                                                                                function(Forms\Get $get, Forms\Set $set){
                                                                                        $unit_model = Models\UnitModel::find($get("unit_model_id"));
                                                                                        $terms_and_amortizations = Models\CustomerApplicationMaintenance::first()->getAttributes()['monthly_amortizations'];
                                                                                        if($unit_model){
                                                                                                $unit_model = Models\UnitModel::find($get("unit_model_id"));
                                                                                                $terms_and_amortizations = Models\CustomerApplicationMaintenance::first()->getAttributes()['monthly_amortizations'];
                                                                                                if($unit_model != null){
                                                                                                        $dp_amount = $unit_model->down_payment_amount;
                                                                                                        $monthly_payment = $unit_model->price * (float)Models\DealerhipCalculations::getAmortizationByTerm($terms_and_amortizations, $get('unit_term'));
                                                                                                        $amount_to_be_financed = (int)$get('unit_term') * $monthly_payment;
        
                                                                                                        $set('total_price', $amount_to_be_financed + $dp_amount);
                                                                                                        $set('unit_ttl_dp', $dp_amount);
                                                                                                        $set('unit_monthly_amort_fin', $monthly_payment);
                                                                                                        $set('amount_to_be_financed', $amount_to_be_financed);
                                                                                                        $set('unit_srp', $unit_model->price);
                                                                                                }
                                                                                        }
                                                                                }
                                                                ),
                                        ]),
                                        Forms\Components\Placeholder::make('available_stock')
                                                ->columnSpan(1)
                                                ->label("Available Stock")
                                                ->hidden(
                                                        function(Forms\Get $get, Forms\Set $set):string{
                                                                $unit_model_id = $get('unit_model_id');
                                                                $preferred_unit = $get('preffered_unit_status');
                                                                if($unit_model_id != null && $preferred_unit != null){
                                                                        return false;
                                                                }
                                                                return true;
                                                        }
                                                )
                                                ->live(onBlur:true)
                                                ->content(
                                                        function(Forms\Get $get, Forms\Set $set):string{
                                                                $unit_model_id = $get('unit_model_id');
                                                                $preferred_unit = $get('preffered_unit_status');
                                                                return Models\Unit::getStockBasedOnUnitAndStatus($unit_model_id, $preferred_unit);
                                                        }
                                                ),
                                        ]),
                                Forms\Components\Fieldset::make("")
                                ->columnSpan(1)
                                ->columns(6)
                                ->schema([
                                        Forms\Components\TextInput::make('unit_monthly_amort_fin')
                                                ->columnSpan(6)
                                                ->readOnly()
                                                ->label('Monthly Payment'),
                                        Forms\Components\TextInput::make('unit_ttl_dp')
                                                ->columnSpan(3)
                                                ->readOnly()
                                                ->label('Down Payment'),
                                        Forms\Components\TextInput::make('amount_to_be_financed')
                                                ->columnSpan(3)
                                                ->readOnly(true)
                                                ->numeric()
                                                ->minValue(1)
                                                ->label('Amount to be financed'),
                                        Forms\Components\TextInput::make('total_price')
                                                ->columnSpan(6)
                                                ->hint("Down Payment added to amount to be financed")
                                                ->readOnly(true)
                                                ->numeric()
                                                ->minValue(1)
                                                ->label('Total Cost'),
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
                                        ->regex('/^[a-zA-Z\s]+$/')
                                        ->label('First Name:')
                                        ->required(true)
                                        ->hidden(
                                            function(?Model $record):bool{
                                                    if($record!=null){
                                                        if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                            $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                            return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'co_maker_fname_textinput');
                                                        }
                                                    }
                                                    return false;
                                            }
                                        )
                                        ->disabled(
                                            function(?Model $record):bool{
                                                if($record!=null){
                                                    if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                        $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                        return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'co_maker_fname_textinput');
                                                    }
                                                }
                                                return false;
                                        }
                                        ),
                                Forms\Components\TextInput::make('co_owner_middlename')
                                        ->columnSpan(2)
                                        ->regex('/^[a-zA-Z\s]+$/')
                                        ->label('Middle Name:')
                                        ->hidden(
                                            function(?Model $record):bool{
                                                if($record!=null){
                                                    if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                        $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                        return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'co_maker_mname_textinput');
                                                    }
                                                }
                                                return false;
                                            }
                                        )
                                        ->disabled(
                                            function(?Model $record):bool{
                                                    if($record!=null){
                                                        if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                            $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                            return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'co_maker_mname_textinput');
                                                        }
                                                    }
                                                    return false;
                                            }
                                        ),
                                Forms\Components\TextInput::make('co_owner_lastname')
                                        ->columnSpan(2)
                                        ->regex('/^[a-zA-Z\s]+$/')
                                        ->label('Last Name:')
                                        ->required(true)
                                        ->hidden(
                                            function(?Model $record):bool{
                                                if($record!=null){
                                                    if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                        $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                        return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'co_maker_lname_textinput');
                                                    }
                                                }
                                                return false;
                                            }
                                        )
                                        ->disabled(
                                            function(?Model $record):bool{
                                                if($record!=null){
                                                    if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                        $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                        return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'co_maker_lname_textinput');
                                                    }
                                                }
                                                return false;
                                            }
                                        ),
                                Forms\Components\TextInput::make('co_owner_email')
                                        ->required()
                                        ->email()
                                        ->columnSpan(6)
                                        ->label('Email:')
                                        ->hidden(
                                            function(?Model $record):bool{
                                                if($record!=null){
                                                    if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                        $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                        return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'co_maker_email_textinput');
                                                    }
                                                }
                                                return false;
                                            }
                                        )
                                        ->disabled(
                                            function(?Model $record):bool{
                                                if($record!=null){
                                                    if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                        $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                        return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'co_maker_email_textinput');
                                                    }
                                                }
                                                return false;
                                            }
                                        ),
                                Forms\Components\DatePicker::make('co_owner_birthday')
                                        ->columnSpan(3)
                                        ->label('Birthday:')
                                        ->maxDate(now()->subYears(150))
                                        ->maxDate(now())
                                        ->required(true)
                                        ->hidden(
                                            function(?Model $record):bool{
                                                if($record!=null){
                                                    if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                        $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                        return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'co_maker_bday_datetime');
                                                    }
                                                }
                                                return false;
                                            }
                                        )
                                        ->disabled(
                                            function(?Model $record):bool{
                                                if($record!=null){
                                                    if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                        $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                        return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'co_maker_bday_datetime');
                                                    }
                                                }
                                                return false;
                                            }
                                        ),
                                Forms\Components\TextInput::make('co_owner_mobile_number')
                                        ->required()
                                        ->numeric()
                                        ->columnSpan(3)
                                        ->label('Contact Number:')
                                        ->hidden(
                                            function(?Model $record):bool{
                                                if($record!=null){
                                                    if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                        $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                        return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'co_maker_contact_textinput');
                                                    }
                                                }
                                                return false;
                                            }
                                        )
                                        ->disabled(
                                            function(?Model $record):bool{
                                                if($record!=null){
                                                    if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                        $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                        return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'co_maker_contact_textinput');
                                                    }
                                                }
                                                return false;
                                            }
                                        ),
                                Forms\Components\TextArea::make('co_owner_address')
                                        ->columnSpan(6)
                                        ->label('Address')
                                        ->required(true)
                                        ->hidden(
                                            function(?Model $record):bool{
                                                if($record!=null){
                                                    if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                        $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                        return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'co_maker_present_address_textarea');
                                                    }
                                                }
                                                return false;
                                            }
                                        )
                                        ->disabled(
                                            function(?Model $record):bool{
                                                if($record!=null){
                                                    if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                        $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                        return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'co_maker_present_address_textarea');
                                                    }
                                                }
                                                return false;
                                            }
                                        ),
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
                                        ->columnSpan(3)
                                        ->hidden(
                                            function(?Model $record):bool{
                                                if($record!=null){
                                                    if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                        $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                        return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'co_maker_id_fileupload');
                                                    }
                                                }
                                                return false;
                                            }
                                        )
                                        ->disabled(
                                            function(?Model $record):bool{
                                                if($record!=null){
                                                    if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                        $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                        return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'co_maker_id_fileupload');
                                                    }
                                                }
                                                return false;
                                            }
                                        ),
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
                                    ->live()
                                    ->label('First Name:')
                                    ->regex('/^[a-zA-Z\s]+$/')
                                    ->columnSpan(2)
                                    ->required(true)
                                    ->hidden(
                                            function(?Model $record):bool{
                                                if($record!=null){
                                                    if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                        $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                        return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_fname_textinput');
                                                    }
                                                }
                                            return false;
                                        }
                                    )
                                    ->disabled(
                                        function(?Model $record):bool{
                                            if($record!=null){
                                                if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                    $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                    return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_fname_textinput');
                                                }
                                            }
                                        return false;
                                    }),
                            Forms\Components\TextInput::make('applicant_middlename')
                                    ->label('Middle Name:')
                                    ->alpha()
                                    ->columnSpan(2)
                                    ->hidden(
                                        function(?Model $record):bool{
                                            if($record!=null){
                                                if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                    $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                    return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_mname_textinput');
                                                }
                                            }
                                        return false;
                                    })
                                    ->disabled(
                                        function(?Model $record):bool{
                                            if($record!=null){
                                                if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                    $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                    return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_mname_textinput');
                                                }
                                            }
                                        return false;
                                    }),
                            Forms\Components\TextInput::make('applicant_lastname')
                                    ->label('Last Name:')
                                    ->regex('/^[a-zA-Z\s]+$/')
                                    ->columnSpan(2)
                                    ->required(true)
                                    ->hidden(
                                        function(?Model $record):bool{
                                            if($record!=null){
                                                if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                    $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                    return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_lname_textinput');
                                                }
                                            }
                                        return false;
                                    })
                                    ->disabled(
                                        function(?Model $record):bool{
                                            if($record!=null){
                                                if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                    $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                    return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_lname_textinput');
                                                }
                                            }
                                        return false;
                                    }),
                            Forms\Components\DatePicker::make('applicant_birthday')
                                    ->label('Birthday:')
                                    ->maxDate(now()->subYears(150))
                                    ->maxDate(now())
                                    ->columnSpan(3)
                                    ->required(true)
                                    ->hidden(
                                        function(?Model $record):bool{
                                            if($record!=null){
                                                if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                    $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                    return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_bday_datetime');
                                                }
                                            }
                                        return false;
                                        })
                                    ->disabled(
                                        function(?Model $record):bool{
                                            if($record!=null){
                                                if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                    $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                    return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_bday_datetime');
                                                }
                                            }
                                        return false;
                                    }),
                            Forms\Components\Select::make('applicant_civil_status')
                                        ->label('Civil Status')
                                        ->live()
                                        ->columnSpan(3)
                                        ->required(true)
                                        ->options(['single'=> 'Single', 'married' => 'Married', 'separated' => 'Separated', 'widow' => 'Widow'])
                                        ->hidden(
                                            function(?Model $record):bool{
                                                if($record!=null){
                                                    if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                        $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                        return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_civil_status_select');
                                                    }
                                                }
                                            return false;
                                            })
                                        ->disabled(
                                            function(?Model $record):bool{
                                                if($record!=null){
                                                    if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                        $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                        return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_civil_status_select');
                                                    }
                                                }
                                            return false;
                                        }),
        ])
        ->columnSpan(3)
        ->columns(6),
        Forms\Components\Fieldset::make("Contact")
                ->schema([
                        Forms\Components\TextInput::make('applicant_telephone')
                                ->columnSpan(3)
                                ->numeric()
                                ->label('Contact Number:')
                                ->required()
                                ->hidden(
                                    function(?Model $record): bool {
                                        if($record!=null){
                                            if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_contact_textinput');
                                            }
                                        }
                                    return false;
                                    }
                                )
                                ->disabled(
                                    function(?Model $record): bool {
                                        if($record!=null){
                                            if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_contact_textinput');
                                            }
                                        }
                                    return false;
                                    }
                                ),
                        Forms\Components\TextInput::make('applicant_email')
                                ->columnSpan(3)
                                ->regex('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/')
                                ->required()
                                ->label('Email:')
                                ->hidden(
                                    function(?Model $record): bool {
                                        if($record!=null){
                                            if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_email_textinput');
                                            }
                                        }
                                    return false;
                                    }
                                )
                                ->disabled(
                                    function(?Model $record): bool {
                                        if($record!=null){
                                            if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_email_textinput');
                                            }
                                        }
                                    return false;
                                    }
                                ),
                                
                ])
        ->columnSpan(3)
        ->columns(6),
        Forms\Components\Group::make([
                Forms\Components\TextInput::make('applicant_lived_there')
                        ->columnSpan(3)
                        ->numeric()
                        ->suffix('year(s)')
                        ->inputMode('integer')
                        ->label('Lived There:')
                        ->hidden(
                            function(?Model $record): bool {
                                if($record!=null){
                                    if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                        $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                        return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_lived_there_numeric');
                                    }
                                }
                            return false;
                            }
                        )
                        ->disabled(
                            function(?Model $record): bool {
                                if($record!=null){
                                    if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                        $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                        return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_lived_there_numeric');
                                    }
                                }
                            return false;
                            }
                        ),                        
                        
                Forms\Components\Select::make('applicant_house')
                        ->columnSpan(3)
                        ->label('House:')
                        ->options(['owned' => 'Owned', 'rented' => 'Rented', 'w/ parents' => 'W/ parents'])
                        ->hidden(
                            function(?Model $record): bool {
                                if($record!=null){
                                    if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                        $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                        return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_house_select');
                                    }
                                }
                            return false;
                            }
                        )
                        ->disabled(
                            function(?Model $record): bool {
                                if($record!=null){
                                    if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                        $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                        return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_house_select');
                                    }
                                }
                            return false;
                            }
                        ),
                Forms\Components\Textarea::make('applicant_present_address')
                        ->columnSpan(6)
                        ->label('Present Address:')
                        ->required(true)
                        ->hidden(
                            function(?Model $record): bool {
                                if($record!=null){
                                    if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                        $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                        return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_present_address_textarea');
                                    }
                                }
                            return false;
                            }
                        )
                        ->disabled(
                            function(?Model $record): bool {
                                if($record!=null){
                                    if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                        $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                        return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_present_address_textarea');
                                    }
                                }
                            return false;
                            }
                        ),
                Forms\Components\Textarea::make('applicant_previous_address')
                        ->columnSpan(6)
                        ->label('Previous Address:')
                        ->hidden(
                            function(?Model $record): bool {
                                if($record!=null){
                                    if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                        $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                        return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_previous_address_textarea');
                                    }
                                }
                            return false;
                            }
                        )
                        ->disabled(
                            function(?Model $record): bool {
                                if($record!=null){
                                    if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                        $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                        return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_previous_address_textarea');
                                    }
                                }
                            return false;
                            }
                        ),
                Forms\Components\Textarea::make('applicant_provincial_address')
                        ->columnSpan(6)
                        ->label('Provincial Address:')
                        ->hidden(
                            function(?Model $record): bool {
                                if($record!=null){
                                    if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                        $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                        return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_provincial_address_textarea');
                                    }
                                }
                            return false;
                            }
                        )
                        ->disabled(
                            function(?Model $record): bool {
                                if($record!=null){
                                    if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                        $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                        return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_provincial_address_textarea');
                                    }
                                }
                            return false;
                            }
                        ),
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
                ->columnSpan(3)
                ->hidden(
                    function(?Model $record):bool{
                            if($record!=null){
                                if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                    $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                    return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_id_fileupload');
                                }
                            }
                        return false;
                    }
                )
                ->disabled(
                    function(?Model $record):bool{
                            if($record!=null){
                                if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                    $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                    return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_id_fileupload');
                                }
                            }
                        return false;
                    }
                ),
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
                                                ->label("Years")
                                                ->columnSpan(1)
                                                ->numeric()
                                                ->suffix("year(s)"),
                                        Forms\Components\TextInput::make("school")
                                                ->columnSpan(2),
                                        Forms\Components\DatePicker::make("year_grad")
                                                ->label("Year Graduate")
                                                ->columnSpan(1),
                                ])
                                ->columns(3)
                                ->columnSpan(3)
                                ->hidden(
                                    function(?Model $record):bool{
                                        if($record!=null){
                                            if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'educational_attainment');
                                            }
                                        }
                                        return false;
                                    }
                                )
                                ->disabled(
                                    function(?Model $record):bool{
                                        if($record!=null){
                                            if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'educational_attainment');
                                            }
                                        }
                                        return false;
                                    }
                                ),
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
                                ->columnSpan(3)
                                ->hidden(
                                    function(?Model $record):bool{
                                        if($record!=null){
                                            if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'dependents');
                                            }
                                        }
                                        return false;
                                    }
                                )
                                ->disabled(
                                    function(?Model $record):bool{
                                        if($record!=null){
                                            if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'dependents');
                                            }
                                        }
                                        return false;
                                    }
                                ),
        ])
        ->columnSpan(6)
        ->columns(6);
    }

    public static function getReferences(): Forms\Components\Component
    {
        return Forms\Components\Group::make([
                Forms\Components\Group::make([
                Forms\Components\Repeater::make('personal_references')
                        ->label("Personal Reference")
                        ->columnSpanFull()
                        ->columnSpan(1)
                        ->columns(4)
                        ->label("Applicant's Personal References")
                        ->collapsible(true)
                        ->schema([
                                Forms\Components\TextInput::make('name')
                                        ->required(),
                                Forms\Components\TextInput::make('address')
                                        ->required(),
                                Forms\Components\Select::make('relationship')
                                        ->required()
                                        ->options(
                                                Enums\RelationshipStatus::class
					                    ),
                                Forms\Components\TextInput::make('telephone')
                                        ->label("Contact Number")
                                        ->numeric()
                                        ->required(),
                        ])
                        ->hidden(
                            function(?Model $record):bool{
                                if($record!=null){
                                    if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                        $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                        return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_personal_references');
                                    }
                                }
                                return false;
                            }
                        )
                        ->disabled(
                            function(?Model $record):bool{
                                if($record!=null){
                                    if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                        $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                        return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_personal_references');
                                    }
                                }
                                return false;
                            }
                        ),
                Forms\Components\Fieldset::make("Applicant's Credit Card Information")
                        ->columns(12)
                        ->schema([
                                Forms\Components\Repeater::make('applicants_card_information')
                                        ->columnSpan(12)
                                        ->schema([
                                                Forms\Components\Select::make('bank_acc_type')
                                                        ->label("Account Type")
                                                        ->columnSpan(4)
                                                        ->options(
                                                                Enums\BankAccountType::class
                                                        ),
                                                Forms\Components\TextInput::make('account_number')
                                                        ->numeric()
                                                        ->label("Account Number")
                                                        ->minLength(12)
                                                        ->hint('Card number must be of exact twelve (12) digits.')
                                                        ->maxLength(12)
                                                        ->columnSpan(8),
                                                Forms\Components\TextInput::make('bank_or_branch')
                                                        ->label("Bank/Branch")
                                                        ->columnSpan(12),
                                                Forms\Components\DatePicker::make('date_openned')
                                                        ->columnSpan(4)
                                                        ->label("Date Openned")
                                                        ->minDate(now()->subYears(150))
                                                        ->maxDate(now()),
                                                Forms\Components\TextInput::make('average_monthly_balance')
                                                        ->columnSpan(4)
                                                        ->label("Average Mo. Balance")
                                                        ->numeric(),
                                        ]),
                        ])
                        ->hidden(
                            function(?Model $record):bool{
                                if($record!=null){
                                    if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                        $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                        return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_credit_card');
                                    }
                                }
                                return false;
                            }
                        )
                        ->disabled(
                            function(?Model $record):bool{
                                if($record!=null){
                                    if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                        $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                        return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_credit_card');
                                    }
                                }
                                return false;
                            }
                        ),
                Forms\Components\Fieldset::make("Credit Card (Creditor's)")
                        ->columns(12)
                        ->schema([
                                Forms\Components\Repeater::make('creditors_card_information')
                                        ->columnSpan(12)
                                        ->schema([
                                            Forms\Components\TextInput::make('credit_card_company')
                                                    ->columnSpan(4)
                                                    ->label("Credit Card Company"),
                                            Forms\Components\TextInput::make('card_number')
                                                    ->columnSpan(8)
                                                    ->label("Card Number")
                                                    ->minLength(12)
                                                    ->numeric()
                                                    ->hint('Card number must be of exact twelve (12) digits.')
                                                    ->maxLength(12),
                                            Forms\Components\DatePicker::make('card_date_issued')
                                                    ->columnSpan(4)
                                                    ->label("Date Issued")
                                                    ->minDate(now()->subYears(150))
                                                    ->maxDate(now()),
                                            Forms\Components\TextInput::make('credit_limit')
                                                    ->columnSpan(4)
                                                    ->numeric()
                                                    ->label("Credit Limit"),
                                            Forms\Components\TextInput::make('outstanding_balance')
                                                    ->numeric()
                                                    ->columnSpan(4)
                                                    ->label("Outstanding Balance"),
                                        ]),
                        ])
                        ->hidden(
                            function(?Model $record):bool{
                                if($record!=null){
                                    if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                        $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                        return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'creditor_credit_card');
                                    }
                                }
                                return false;
                            }
                        )
                        ->disabled(
                            function(?Model $record):bool{
                                if($record!=null){
                                    if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                        $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                        return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'creditor_credit_card');
                                    }
                                }
                                return false;
                            }
                        ),
                Forms\Components\Fieldset::make('Creditor Information')
                        ->columns(12)
                        ->schema([
                                Forms\Components\Repeater::make('creditors_information')
                                        ->columns(12)
                                        ->columnSpan(12)
                                        ->schema([
                                                Forms\Components\TextInput::make('name')
                                                        ->label('Creditor Name:')
                                                        ->columnSpan(4),
                                                Forms\Components\TextInput::make('term')
                                                        ->numeric()
                                                        ->columnSpan(2),
                                                Forms\Components\TextInput::make('present_balance')
                                                        ->columnSpan(2)
                                                        ->numeric(),
                                                Forms\Components\TextInput::make('principal')
                                                        ->numeric()
                                                        ->columnSpan(2),
                                                Forms\Components\TextInput::make('monthly_amorthization')
                                                        ->columnSpan(2)
                                                        ->numeric(),
                                        ]),
                        ])
                        ->hidden(
                            function(?Model $record):bool{
                                if($record!=null){
                                    if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                        $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                        return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'creditor_information');
                                    }
                                }
                                return false;
                            }
                        )
                        ->disabled(
                            function(?Model $record):bool{
                                if($record!=null){
                                    if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                        $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                        return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'creditor_information');
                                    }
                                }
                                return false;
                            }
                        ),
            ]),
        ]);
    }

    public static function getEmployment(): Forms\Components\Component
    {
        return Forms\Components\Group::make()
            ->columns(2)
            ->schema([
                    Forms\Components\Fieldset::make("Applicant's Present Employer")
                            ->columns(2)
                            ->columnSpan(1)
                            ->schema([
                                    Forms\Components\TextArea::make('applicant_present_business_employer')
                                            ->label('Employer')
                                            ->required()
                                            ->columnSpan(2)
                                            ->hidden(
                                                function(?Model $record):bool{
                                                    if($record!=null){
                                                        if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                            $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                            return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_present_employer');
                                                        }
                                                    }
                                                    return false;
                                                }
                                            )
                                            ->disabled(
                                                function(?Model $record):bool{
                                                    if($record!=null){
                                                        if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                            $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                            return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_present_employer');
                                                        }
                                                    }
                                                    return false;
                                                }
                                            ),
                                    Forms\Components\TextInput::make('applicant_position')
                                            ->required()
                                            ->label('Position')
                                            ->columnSpan(1)
                                            ->hidden(
                                                function(?Model $record):bool{
                                                    if($record!=null){
                                                        if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                            $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                            return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_position');
                                                        }
                                                    }
                                                    return false;
                                                }
                                            )
                                            ->disabled(
                                                function(?Model $record):bool{
                                                    if($record!=null){
                                                        if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                            $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                            return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_position');
                                                        }
                                                    }
                                                    return false;
                                                }
                                            ),
                                    Forms\Components\TextInput::make('applicant_how_long_job_or_business')
                                            ->required()
                                            ->label('How long')
                                            ->columnSpan(1)
                                            ->hidden(
                                                function(?Model $record):bool{
                                                    if($record!=null){
                                                        if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                            $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                            return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_how_long_employed');
                                                        }
                                                    }
                                                    return false;
                                                }
                                            )
                                            ->disabled(
                                                function(?Model $record):bool{
                                                    if($record!=null){
                                                        if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                            $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                            return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_how_long_employed');
                                                        }
                                                    }
                                                    return false;
                                                }
                                            ),
                            ]),
                    Forms\Components\Fieldset::make("Applicant's Business")
                            ->columnSpan(1)
                            ->columns(12)
                            ->schema([
                                    Forms\Components\TextArea::make('applicant_business_address')
                                            ->label('Address')
                                            ->columnSpan(6),
                                    Forms\Components\TextArea::make('applicant_nature_of_business')
                                            ->columnSpan(6)
                                            ->label('Nature of Business'),
                            ])
                            ->hidden(
                                    function(?Model $record):bool{
                                        if($record!=null){
                                            if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_business');
                                            }
                                        }
                                        return false;
                                    }
                            )
                            ->disabled(
                                    function(?Model $record):bool{
                                        if($record!=null){
                                            if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                                return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_business');
                                            }
                                        }
                                        return false;
                                    }
                            ),                             
                    Forms\Components\Fieldset::make("Previous Employer")
                            ->columnSpan(2)
                            ->columns(1)
                            ->schema([
                                    Forms\Components\TextArea::make('applicant_previous_employer')
                                            ->label('Employer')
                                            ->columnSpan(1),
                                    Forms\Components\TextArea::make('applicant_previous_employer_position')
                                            ->label('Position')
                                            ->columnSpan(1),
                                    Forms\Components\TextArea::make('applicant_how_long_prev_job_or_business')
                                            ->label('How Long')
                                            ->columnSpan(1),
                            ])
                            ->hidden(
                                function(?Model $record):bool{
                                    if($record!=null){
                                        if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                            $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                            return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_previous_employer');
                                        }
                                    }
                                    return false;
                                }
                            )
                            ->disabled(
                                function(?Model $record):bool{
                                    if($record!=null){
                                        if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                            $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                            return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_previous_employer');
                                        }
                                    }
                                    return false;
                                }
                            ),
                    Forms\Components\Fieldset::make("Spouse's Employement Information")
                            ->schema([
                                    Forms\Components\Fieldset::make("Present Employer")
                                    ->columns(2)
                                    ->columnSpan(2)
                                    ->schema([
                                            Forms\Components\TextArea::make('spouse_employer')
                                                    ->label('Business Employer')
                                                    ->columnSpan(2),
                                            Forms\Components\TextInput::make('spouse_position')
                                                    ->label('Position')
                                                    ->columnSpan(1),
                                            Forms\Components\TextInput::make('spouse_how_long_job_business')
                                                    ->label('How long')
                                                    ->columnSpan(1),
                                    ]),
                                    Forms\Components\Fieldset::make("Business")
                                            ->columnSpan(1)
                                            ->columns(1)
                                            ->schema([
                                                    Forms\Components\TextArea::make('spouse_business_address')
                                                            ->label('Address:')
                                                            ->columnSpan(1),
                                                    Forms\Components\TextInput::make('spouse_nature_of_business')
                                                            ->label('Nature of Business:'),
                                            ]),  
                            ])->hidden(fn (Forms\Get $get): bool => $get('applicant_civil_status') != "married")
            ]);
    }

    public static function getProperties(): Forms\Components\Component
    {
        return Forms\Components\Group::make([
            Forms\Components\Section::make("")
					->label("Propeties")
                    ->columns(12)
                    ->schema([
                            Forms\Components\TextInput::make('number_of_vehicles')
                                    ->columnSpan(4)
                                    ->numeric()
                                    ->minValue(0),
                            Forms\Components\Repeater::make('real_estate_property')
                                    ->columnSpan(4)
                                    ->schema([
                                            Forms\Components\Select::make('type')
                                                    ->options(
                                                            Enums\RealEstateType::class
                                                    ),
                                            Forms\Components\TextInput::make('clean'),
                                            Forms\Components\TextInput::make('mortgage')
                                                    ->numeric()
                                                    ->minValue(0),
                                            Forms\Components\TextInput::make('to_whom')
                                                    ->regex('/^[a-zA-Z\s]+$/'),
                                            Forms\Components\TextInput::make('market_value')
                                                    ->numeric()
                                                    ->minValue(0),
                                    ]),
                            Forms\Components\Repeater::make('appliance_property')
                                            ->columnSpan(4)
                                            ->schema([
                                                    Forms\Components\TextInput::make('name'),
                                            ]),
                    ])
                    ->hidden(
                        function(?Model $record):bool{
                            if($record!=null){
                                if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                    $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                    return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'properties');
                                }
                            }
                            return false;
                        }
                    )
                    ->disabled(
                        function(?Model $record):bool{
                            if($record!=null){
                                if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                    $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                    return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'properties');
                                }
                            }
                            return false;
                        }
                    ),

        ]);
    }

    public static function getIncome(): Forms\Components\Component
    {
        return new Forms\Components\Group([
                Forms\Components\Group::make()
                        ->columns(12)
                        ->columnSpan(12)
                        ->schema([
                            Forms\Components\Section::make("Applicant's Income")
                            ->columns(12)
                            ->columnSpan(6)
                            ->schema([
                                    Forms\Components\TextInput::make("applicants_basic_monthly_salary")
                                            ->columnSpan(4)
                                            ->label("Basic Monthly Salary:")
                                            ->live(onBlur: true)
                                            ->inputMode('decimal')
                                            ->required()
                                            ->minValue(0)
                                            ->default(0)
                                            ->numeric()
                                            ->afterStateUpdated(function (Forms\Components\TextInput $component, ?string $state, Forms\Get $get, Forms\Set $set) {
                                                if($state == null){
                                                    $component->state(0);
                                                }
                                                $deductions = $get('applicants_deductions');
                                                $TOTAL_EXPENSE_WITH_MONTHLY_PAYMENT = DealerhipCalculations::calculateSum(
                                                        $get('total_expenses'),
                                                        $deductions,
                                                );
                                                $additionals = DealerhipCalculations::calculateSum(
                                                        $get('applicants_basic_monthly_salary'),
                                                        $get('applicants_allowance_commission')
                                                );
                                                $net = Models\StatementOfMonthlyIncomeHelper::calculateNetIncome(
                                                        $additionals,
                                                        $deductions,
                                                );
                                                $gross = DealerhipCalculations::calculateSum(
                                                        $get('spouse_net_monthly_income'),
                                                        $net,
                                                        $get('other_income')
                                                );
                                                $overall = Models\StatementOfMonthlyIncomeHelper::calculateNetIncome(
                                                        $gross,
                                                        $TOTAL_EXPENSE_WITH_MONTHLY_PAYMENT,
                                                );
                                                $set('applicants_net_monthly_income', $net);
                                                $set('gross_monthly_income', $gross);
                                                $set('net_monthly_income', $overall);
                                            }),
                                    Forms\Components\TextInput::make("applicants_allowance_commission")
                                            ->columnSpan(4)
                                            ->live(onBlur: true)
                                            ->inputMode('decimal')
                                            ->label("Allowance Commision:")
                                            ->minValue(0)
                                            ->default(0)
                                            ->numeric()
                                            ->afterStateUpdated(function (Forms\Components\TextInput $component, ?string $state, Forms\Get $get, Forms\Set $set) {
                                                if($state == null){
                                                    $component->state(0);
                                                }
                                                $deductions = $get('applicants_deductions');
                                                $TOTAL_EXPENSE_WITH_MONTHLY_PAYMENT = DealerhipCalculations::calculateSum(
                                                        $get('total_expenses'),
                                                        $deductions,
                                                );
                                                $additionals = DealerhipCalculations::calculateSum(
                                                        $get('applicants_basic_monthly_salary'),
                                                        $get('applicants_allowance_commission')
                                                );
                                                $net = Models\StatementOfMonthlyIncomeHelper::calculateNetIncome(
                                                        $additionals,
                                                        $deductions,
                                                );
                                                $gross = DealerhipCalculations::calculateSum(
                                                        $get('spouse_net_monthly_income'),
                                                        $net,
                                                        $get('other_income')
                                                );
                                                $overall = Models\StatementOfMonthlyIncomeHelper::calculateNetIncome(
                                                        $gross,
                                                        $TOTAL_EXPENSE_WITH_MONTHLY_PAYMENT,
                                                );
                                                $set('applicants_net_monthly_income', $net);
                                                $set('gross_monthly_income', $gross);
                                                $set('net_monthly_income', $overall);
                                            }),
                                    Forms\Components\TextInput::make("applicants_deductions")
                                            ->columnSpan(4)
                                            ->live(onBlur: true)
                                            ->inputMode('decimal')
                                            ->label("Deductions:")
                                            ->minValue(0)
                                            ->default(0)
                                            ->numeric()
                                            ->afterStateUpdated(function (Forms\Components\TextInput $component, ?string $state, Forms\Get $get, Forms\Set $set) {
                                                    if($state == null){
                                                        $component->state(0);
                                                    }
                                                    $deductions = $get('applicants_deductions');
                                                    $TOTAL_EXPENSE_WITH_MONTHLY_PAYMENT = DealerhipCalculations::calculateSum(
                                                            $get('total_expenses'),
                                                            $deductions,
                                                    );
                                                    $additionals = DealerhipCalculations::calculateSum(
                                                            $get('applicants_basic_monthly_salary'),
                                                            $get('applicants_allowance_commission')
                                                    );
                                                    $net = Models\StatementOfMonthlyIncomeHelper::calculateNetIncome(
                                                            $additionals,
                                                            $deductions,
                                                    );
                                                    $gross = DealerhipCalculations::calculateSum(
                                                            $get('spouse_net_monthly_income'),
                                                            $net,
                                                            $get('other_income')
                                                    );
                                                    $overall = Models\StatementOfMonthlyIncomeHelper::calculateNetIncome(
                                                            $gross,
                                                            $TOTAL_EXPENSE_WITH_MONTHLY_PAYMENT,
                                                    );
                                                    $set('applicants_net_monthly_income', $net);
                                                    $set('gross_monthly_income', $gross);
                                                    $set('net_monthly_income', $overall);
                                            }),
                                    Forms\Components\TextInput::make("applicants_net_monthly_income")
                                            ->live(onBlur: true)
                                            ->inputMode('decimal')
                                            ->label("Net Monthly Income:")
                                            ->default(0)
                                            ->readOnly()
                                            ->minValue(0)
                                            ->numeric()
                                            ->columnSpan(12)
                                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                                $deductions = $get('applicants_deductions');
                                                $additionals = DealerhipCalculations::calculateSum(
                                                        $get('applicants_basic_monthly_salary'),
                                                        $get('applicants_allowance_commission')
                                                );
                                                $deductions = $get('applicants_deductions');
                                                $TOTAL_EXPENSE_WITH_MONTHLY_PAYMENT = DealerhipCalculations::calculateSum(
                                                        $get('total_expenses'),
                                                        $deductions,
                                                );
                                                $additionals = DealerhipCalculations::calculateSum(
                                                        $get('applicants_basic_monthly_salary'),
                                                        $get('applicants_allowance_commission')
                                                );
                                                $net = Models\StatementOfMonthlyIncomeHelper::calculateNetIncome(
                                                        $additionals,
                                                        $deductions,
                                                );
                                                $gross = DealerhipCalculations::calculateSum(
                                                        $get('spouse_net_monthly_income'),
                                                        $net,
                                                        $get('other_income')
                                                );
                                                $overall = Models\StatementOfMonthlyIncomeHelper::calculateNetIncome(
                                                        $gross,
                                                        $TOTAL_EXPENSE_WITH_MONTHLY_PAYMENT,
                                                );
                                                $set('applicants_net_monthly_income', $net);
                                                $set('gross_monthly_income', $gross);
                                                $set('net_monthly_income', $overall);
                                            }),
                            ]),
                            Forms\Components\Section::make("Spouse's Income")
                                    ->columns(12)
                                    ->columnSpan(6)
                                    ->disabled(fn (Forms\Get $get): bool => $get('applicant_civil_status') != "married")
                                    ->schema([
                                            Forms\Components\TextInput::make("spouses_basic_monthly_salary")->label("Basic Monthly Salary:")
                                                    ->columnSpan(4)
                                                    ->label("Basic Monthly Salary")
                                                    ->live(onBlur: true)
                                                    ->inputMode('decimal')
                                                    ->minValue(0)
                                                    ->default(0)
                                                    ->numeric()
                                                    ->afterStateUpdated(function (Forms\Components\TextInput $component, ?string $state, Forms\Get $get, Forms\Set $set) {
                                                            if($state == null){
                                                                    $component->state(0);
                                                            }
                                                            $deductions = $get('spouse_deductions');
                                                            $TOTAL_EXPENSE_WITH_MONTHLY_PAYMENT = DealerhipCalculations::calculateSum(
                                                                $get('total_expenses'),
                                                                $deductions,
                                                            );
                                                            $additionals = DealerhipCalculations::calculateSum(
                                                                    $get('spouses_basic_monthly_salary'),
                                                                    $get('spouse_allowance_commision')
                                                            );
                                                            $net = Models\StatementOfMonthlyIncomeHelper::calculateNetIncome(
                                                                    $additionals,
                                                                    $deductions,
                                                            );
                                                            $gross = DealerhipCalculations::calculateSum(
                                                                    $get('applicants_net_monthly_income'),
                                                                    $net,
                                                                    $get('other_income')
                                                            );
                                                            $overall = Models\StatementOfMonthlyIncomeHelper::calculateNetIncome(
                                                                    $gross,
                                                                    $TOTAL_EXPENSE_WITH_MONTHLY_PAYMENT,
                                                            );
                                                            $set('spouse_net_monthly_income', $net);
                                                            $set('gross_monthly_income', $gross);
                                                            $set('net_monthly_income', $overall);
                                                    }),
                                            Forms\Components\TextInput::make("spouse_allowance_commision")->label("Allowance Commision:")->numeric()
                                                    ->columnSpan(4)
                                                    ->label("Allowance Commision")
                                                    ->live(onBlur: true)
                                                    ->inputMode('decimal')
                                                    ->minValue(0)
                                                    ->default(0)
                                                    ->numeric()
                                                    ->afterStateUpdated(function (Forms\Components\TextInput $component, ?string $state, Forms\Get $get, Forms\Set $set) {
                                                            if($state == null){
                                                                    $component->state(0);
                                                            }
                                                            $deductions = $get('spouse_deductions');
                                                            $TOTAL_EXPENSE_WITH_MONTHLY_PAYMENT = DealerhipCalculations::calculateSum(
                                                                $get('total_expenses'),
                                                                $deductions,
                                                            );
                                                            $additionals = DealerhipCalculations::calculateSum(
                                                                    $get('spouses_basic_monthly_salary'),
                                                                    $get('spouse_allowance_commision')
                                                            );
                                                            $net = Models\StatementOfMonthlyIncomeHelper::calculateNetIncome(
                                                                    $additionals,
                                                                    $deductions,
                                                            );
                                                            $gross = DealerhipCalculations::calculateSum(
                                                                    $get('applicants_net_monthly_income'),
                                                                    $net,
                                                                    $get('other_income')
                                                            );
                                                            $overall = Models\StatementOfMonthlyIncomeHelper::calculateNetIncome(
                                                                    $gross,
                                                                    $TOTAL_EXPENSE_WITH_MONTHLY_PAYMENT,
                                                            );
                                                            $set('spouse_net_monthly_income', $net);
                                                            $set('gross_monthly_income', $gross);
                                                            $set('net_monthly_income', $overall);
                                                    }),
                                            Forms\Components\TextInput::make("spouse_deductions")->label("Deductions:")->numeric()
                                                    ->columnSpan(4)
                                                    ->label("Deductions")
                                                    ->live(onBlur: true)
                                                    ->inputMode('decimal')
                                                    ->minValue(0)
                                                    ->default(0)
                                                    ->numeric()
                                                    ->afterStateUpdated(function (Forms\Components\TextInput $component, ?string $state, Forms\Get $get, Forms\Set $set) {
                                                            if($state == null){
                                                                    $component->state(0);
                                                            }
                                                            $deductions = $get('spouse_deductions');
                                                            $TOTAL_EXPENSE_WITH_MONTHLY_PAYMENT = DealerhipCalculations::calculateSum(
                                                                $get('total_expenses'),
                                                                $deductions,
                                                            );
                                                            $additionals = DealerhipCalculations::calculateSum(
                                                                    $get('spouses_basic_monthly_salary'),
                                                                    $get('spouse_allowance_commision')
                                                            );
                                                            $net = Models\StatementOfMonthlyIncomeHelper::calculateNetIncome(
                                                                    $additionals,
                                                                    $deductions,
                                                            );
                                                            $gross = DealerhipCalculations::calculateSum(
                                                                    $get('applicants_net_monthly_income'),
                                                                    $net,
                                                                    $get('other_income')
                                                            );
                                                            $overall = Models\StatementOfMonthlyIncomeHelper::calculateNetIncome(
                                                                    $gross,
                                                                    $TOTAL_EXPENSE_WITH_MONTHLY_PAYMENT,
                                                            );
                                                            $set('spouse_net_monthly_income', $net);
                                                            $set('gross_monthly_income', $gross);
                                                            $set('net_monthly_income', $overall);
                                                    }),
                                            Forms\Components\TextInput::make("spouse_net_monthly_income")->label("Net Monthly Income:")->numeric()
                                                    ->columnSpan(12)
                                                    ->readOnly()
                                                    ->label("Net Monthly Income")
                                                    ->live(onBlur: true)
                                                    ->inputMode('decimal')
                                                    ->minValue(0)
                                                    ->default(0)
                                                    ->numeric()
                                                    ->afterStateUpdated(function (Forms\Components\TextInput $component, ?string $state, Forms\Get $get, Forms\Set $set) {
                                                            if($state == null){
                                                                    $component->state(0);
                                                            }
                                                            $deductions = $get('spouse_deductions');
                                                            $TOTAL_EXPENSE_WITH_MONTHLY_PAYMENT = DealerhipCalculations::calculateSum(
                                                                $get('total_expenses'),
                                                                $deductions,
                                                            );
                                                            $additionals = DealerhipCalculations::calculateSum(
                                                                    $get('spouses_basic_monthly_salary'),
                                                                    $get('spouse_allowance_commision')
                                                            );
                                                            $net = Models\StatementOfMonthlyIncomeHelper::calculateNetIncome(
                                                                    $additionals,
                                                                    $deductions,
                                                            );
                                                            $gross = DealerhipCalculations::calculateSum(
                                                                    $get('applicants_net_monthly_income'),
                                                                    $net,
                                                                    $get('other_income')
                                                            );
                                                            $overall = Models\StatementOfMonthlyIncomeHelper::calculateNetIncome(
                                                                    $gross,
                                                                    $TOTAL_EXPENSE_WITH_MONTHLY_PAYMENT,
                                                            );
                                                            $set('spouse_net_monthly_income', $net);
                                                            $set('gross_monthly_income', $gross);
                                                            $set('net_monthly_income', $overall);
                                                    }),
                                    ]),
                        ]),
                Forms\Components\TextInput::make("other_income")->label("Other Income:")->numeric()
                        ->columnSpan(12)
                        ->label("Other Income")
                        ->live(onBlur: true)
                        ->inputMode('decimal')
                        ->minValue(0)
                        ->default(0)
                        ->numeric() 
                        ->afterStateUpdated(function (Forms\Components\TextInput $component, ?string $state, Forms\Get $get, Forms\Set $set) {
                                if($state == null){
                                        $component->state(0);
                                }
                                $gross = DealerhipCalculations::calculateSum(
                                    $get('applicants_net_monthly_income'),
                                    $get('spouse_net_monthly_income'),
                                    $get('other_income')
                                );
                                $TOTAL_EXPENSE_WITH_MONTHLY_PAYMENT = DealerhipCalculations::calculateSum(
                                    $get('total_expenses'),
                                );
                                $overall = Models\StatementOfMonthlyIncomeHelper::calculateNetIncome(
                                    $gross,
                                    $TOTAL_EXPENSE_WITH_MONTHLY_PAYMENT,
                                );
                                $set('gross_monthly_income', $gross);
                                $set('net_monthly_income', $overall);
                        }),
                Forms\Components\TextInput::make("gross_monthly_income")->label("Gross Monthly Income:")->numeric()->columnSpan(1)
                        ->columnSpan(12)
                        ->label("Gross Monthly Income")
                        ->live(onBlur: true)
                        ->inputMode('decimal')
                        ->minValue(0)
                        ->default(0)
                        ->numeric() 
                        ->afterStateUpdated(function (Forms\Components\TextInput $component, ?string $state, Forms\Get $get, Forms\Set $set) {
                            if($state == null){
                                    $component->state(0);
                            }
                            $gross = DealerhipCalculations::calculateSum(
                                    $get('applicants_net_monthly_income'),
                                    $get('spouse_net_monthly_income'),
                                    $get('other_income')
                            );
                            $set('gross_monthly_income', $gross);
                        }),
        ]);
    }

    public static function getExpenses(): Forms\Components\Component
    {
        return Forms\Components\Group::make([
                Forms\Components\Section::make("Expenses")
                ->columns(12)
                ->schema([
                        Forms\Components\TextInput::make("living_expenses")
                                ->columnSpan(4)
                                ->label("Living Expenses")
                                ->live(onBlur: true)
                                ->inputMode('decimal')
                                ->minValue(0)
                                ->default(0)
                                ->numeric()
                                ->afterStateUpdated(function (Forms\Components\TextInput $component, ?string $state, Forms\Get $get, Forms\Set $set) {
                                        if($state == null){
                                                $component->state(0);
                                        }
                                        $additionals = DealerhipCalculations::calculateSum(
                                                $get('education'),
                                                $get('living_expenses'),
                                                $get('transportation'),
                                                $get('rental'),
                                                $get('utilities'),
                                                $get('other_expenses'),
                                                $get('unit_monthly_amort_fin')
                                        );
                                        $overall = Models\StatementOfMonthlyIncomeHelper::calculateNetIncome(
                                                $get('gross_monthly_income'),
                                                $additionals,
                                        );
                                        $set('total_expenses', $additionals);
                                        $set('net_monthly_income', $overall);
                                }),
                        Forms\Components\TextInput::make("education")
                                ->columnSpan(4)
                                ->label("Education")
                                ->live(onBlur: true)
                                ->inputMode('decimal')
                                ->minValue(0)
                                ->default(0)
                                ->numeric()
                                ->afterStateUpdated(function (Forms\Components\TextInput $component, ?string $state, Forms\Get $get, Forms\Set $set) {
                                        if($state == null){
                                                $component->state(0);
                                        }
                                        $additionals = DealerhipCalculations::calculateSum(
                                                $get('education'),
                                                $get('living_expenses'),
                                                $get('transportation'),
                                                $get('rental'),
                                                $get('utilities'),
                                                $get('other_expenses'),
                                                $get('unit_monthly_amort_fin')
                                        );
                                        $overall = Models\StatementOfMonthlyIncomeHelper::calculateNetIncome(
                                                $get('gross_monthly_income'),
                                                $additionals,
                                        );
                                        $set('total_expenses', $additionals);
                                        $set('net_monthly_income', $overall);
                                }),
                        Forms\Components\TextInput::make("transportation")
                                ->columnSpan(4)
                                ->label("Transportation")
                                ->live(onBlur: true)
                                ->inputMode('decimal')
                                ->minValue(0)
                                ->default(0)
                                ->numeric()
                                ->afterStateUpdated(function (Forms\Components\TextInput $component, ?string $state, Forms\Get $get, Forms\Set $set) {
                                        if($state == null){
                                                $component->state(0);
                                        }
                                        $additionals = DealerhipCalculations::calculateSum(
                                            $get('education'),
                                            $get('living_expenses'),
                                            $get('transportation'),
                                            $get('rental'),
                                            $get('utilities'),
                                            $get('other_expenses'),
                                            $get('unit_monthly_amort_fin')
                                            
                                        );
                                        $overall = Models\StatementOfMonthlyIncomeHelper::calculateNetIncome(
                                                $get('gross_monthly_income'),
                                                $additionals,
                                        );
                                        $set('total_expenses', $additionals);
                                        $set('net_monthly_income', $overall);
                                }),
                        Forms\Components\TextInput::make("rental")
                                ->columnSpan(4)
                                ->label("Rent")
                                ->live(onBlur: true)
                                ->inputMode('decimal')
                                ->minValue(0)
                                ->default(0)
                                ->numeric()
                                ->afterStateUpdated(function (Forms\Components\TextInput $component, ?string $state, Forms\Get $get, Forms\Set $set) {
                                        if($state == null){
                                                $component->state(0);
                                        }
                                        $additionals = DealerhipCalculations::calculateSum(
                                            $get('education'),
                                            $get('living_expenses'),
                                            $get('transportation'),
                                            $get('rental'),
                                            $get('utilities'),
                                            $get('other_expenses'),
                                            $get('unit_monthly_amort_fin')
                                        );
                                        $overall = Models\StatementOfMonthlyIncomeHelper::calculateNetIncome(
                                                $get('gross_monthly_income'),
                                                $additionals,
                                        );
                                        $set('total_expenses', $additionals);
                                        $set('net_monthly_income', $overall);
                                }),
                        Forms\Components\TextInput::make("utilities")->label("Utilities:")->numeric()
                                ->columnSpan(4)
                                ->label("Utilities")
                                ->live(onBlur: true)
                                ->inputMode('decimal')
                                ->minValue(0)
                                ->default(0)
                                ->numeric()
                                ->afterStateUpdated(function (Forms\Components\TextInput $component, ?string $state, Forms\Get $get, Forms\Set $set) {
                                        if($state == null){
                                                $component->state(0);
                                        }
                                        $additionals = DealerhipCalculations::calculateSum(
                                            $get('education'),
                                            $get('living_expenses'),
                                            $get('transportation'),
                                            $get('rental'),
                                            $get('utilities'),
                                            $get('other_expenses'),
                                            $get('unit_monthly_amort_fin')
                                        );
                                        $overall = Models\StatementOfMonthlyIncomeHelper::calculateNetIncome(
                                                $get('gross_monthly_income'),
                                                $additionals,
                                        );
                                        $set('total_expenses', $additionals);
                                        $set('net_monthly_income', $overall);
                                }),
                        Forms\Components\TextInput::make("unit_monthly_amort_fin")
                                        ->label("Monthly Amortization:")
                                        ->numeric()
                                        ->readOnly()
                                        ->columnSpan(4)
                                        ->label("Monthly Amortization")
                                        ->live(onBlur: true)
                                        ->inputMode('decimal')
                                        ->minValue(0)
                                        ->default(0)
                                        ->numeric()
                                        ->afterStateUpdated(function (Forms\Components\TextInput $component, ?string $state, Forms\Get $get, Forms\Set $set) {
                                                if($state == null){
                                                        $component->state(0);
                                                }
                                        }),
                        Forms\Components\TextInput::make("other_expenses")->label("Other Expenses:")->numeric()
                                ->columnSpan(4)
                                        ->label("Other Expenses")
                                        ->live(onBlur: true)
                                        ->inputMode('decimal')
                                        ->minValue(0)
                                        ->default(0)
                                        ->numeric()
                                        ->afterStateUpdated(function (Forms\Components\TextInput $component, ?string $state, Forms\Get $get, Forms\Set $set) {
                                                if($state == null){
                                                        $component->state(0);
                                                }
                                                $additionals = DealerhipCalculations::calculateSum(
                                                        $get('education'),
                                                        $get('living_expenses'),
                                                        $get('transportation'),
                                                        $get('rental'),
                                                        $get('utilities'),
                                                        $get('other_expenses'),
                                                );
                                                $overall = Models\StatementOfMonthlyIncomeHelper::calculateNetIncome(
                                                        $get('gross_monthly_income'),
                                                        $additionals,
                                                );
                                                $set('total_expenses', $additionals);
                                                $set('net_monthly_income', $overall);
                                        }),
                        Forms\Components\TextInput::make("total_expenses")->label("Total Expenses:")->numeric()->columnSpan(1)
                                ->columnSpan(4)
                                ->label("Total Expenses")
                                ->live(onBlur: true)
                                ->readOnly()
                                ->inputMode('decimal')
                                ->minValue(0)
                                ->default(function (Forms\Get $get):float {
                                    return $get("unit_monthly_amort_fin");
                                })
                                ->numeric()
                                ->afterStateUpdated(function (Forms\Components\TextInput $component, ?string $state, Forms\Get $get, Forms\Set $set) {
                                        if($state == null){
                                                $component->state(0+$get("unit_monthly_amort_fin"));
                                        }
                                }),
                ]),
        ]);
    }

    public static function getNetIncome(): Forms\Components\Component
    {
        return Forms\Components\Group::make([
            Forms\Components\Section::make("Net Monthly Income:")
                    ->columns(12)
                    ->schema([
                            Forms\Components\TextInput::make("net_monthly_income")->label("Net Monthly Income:")
                                    ->label("Net Monthly Income")
                                    ->live(onBlur: true)
                                    ->inputMode('decimal')
                                    ->columnSpan(12)
                                    ->minValue(0)
                                    ->readOnly()
                                    ->default(0)
                                    ->numeric()
                                    ->afterStateUpdated(function (Forms\Components\TextInput $component, ?string $state, Forms\Get $get, Forms\Set $set) {
                                            if($state == null){
                                                    $component->state(0);
                                            }
                                    }),
                            Forms\Components\TextInput::make("net_monthly_income_")->label("Net Monthly Income:")
                                    ->label("Net Monthly Income")
                                    ->live(onBlur: true)
                                    ->inputMode('decimal')
                                    ->columnSpan(12)
                                    ->minValue(0)
                                    ->readOnly()
                                    ->default(0)
                                    ->numeric()
                                    ->afterStateUpdated(function (Forms\Components\TextInput $component, ?string $state, Forms\Get $get, Forms\Set $set) {
                                            if($state == null){
                                                    $component->state(0);
                                            }
                                    }),
                            Forms\Components\Toggle::make('include_monthly_payment')
                                    ->label("See Monhtly Payment Included")
                                    ->inline()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function(?string $state, Forms\Get $get, Forms\Set $set){
                                        if($state == true)
                                        {
                                            $deducted = Models\StatementOfMonthlyIncomeHelper::calculateNetIncome(
                                                $get('net_monthly_income'),
                                                $get("unit_monthly_amort_fin"),
                                            );
                                            $set("net_monthly_income", $deducted);
                                        }
                                        else{
                                            $deducted = Models\DealerhipCalculations::calculateSum(
                                                $get('net_monthly_income'),
                                                $get("unit_monthly_amort_fin"),
                                            );
                                            $set("net_monthly_income", $deducted);
                                        }
                                    })
                                    ->columnSpan(12)
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

    public static function getResubmissionNotes(): Forms\Components\Component
    {
        return Forms\Components\Group::make([
                Forms\Components\Textarea::make('resubmission_note')
                        ->columnSpan(1)
                        ->label('Resubmission Note')
                        ->disabled(true)
                        ->hidden(function(?Model $record): bool {
                                if($record != null){
                                        if($record->getStatus() == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                                return false;
                                        }
                                }
                                return true;
                        }),
        ]);
    }

    public static function getRejectionNote(): Infolists\Components\Component
    {
        return Infolists\Components\Split::make([
                Infolists\Components\Section::make([
                            Infolists\Components\TextEntry::make('reject_note')
                            ->label('Reason of rejection')
                            ->markdown()
                            ->prose()
                            ->weight(FontWeight::Bold),
                    ])
                    ->grow(),   
        ]);
    }

    public static function getStatementOfMonthlyIncome(): Forms\Components\Component
    {
        return Forms\Components\Group::make([
            CustomerApplicationResource::getIncome()->columnSpan(12)
                    ->hidden(
                        function(?Model $record):bool{
                            if($record!=null){
                                if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                    $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                    return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'recalculate_monthly_income');
                                }
                            }
                            return false;
                        }
                    )
                    ->disabled(
                        function(?Model $record):bool{
                            if($record!=null){
                                if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                    $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                    return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'recalculate_monthly_income');
                                }
                            }
                            return false;
                        }
                    ),
            CustomerApplicationResource::getExpenses()->columnSpan(12)
                    ->hidden(
                        function(?Model $record):bool{
                            if($record!=null){
                                if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                    $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                    return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'recalculate_monthly_income');
                                }
                            }
                            return false;
                        }
                    )
                    ->disabled(
                        function(?Model $record):bool{
                            if($record!=null){
                                if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                    $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                    return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'recalculate_monthly_income');
                                }
                            }
                            return false;
                        }
                    ),
            CustomerApplicationResource::getNetIncome()->columnSpan(12)
                    ->hidden(
                        function(?Model $record):bool{
                            if($record!=null){
                                if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                    $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                    return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'recalculate_monthly_income');
                                }
                            }
                            return false;
                        }
                    )
                    ->disabled(
                        function(?Model $record):bool{
                            if($record!=null){
                                if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                    $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                    return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'recalculate_monthly_income');
                                }
                            }
                            return false;
                        }
                    ),
            CustomerApplicationResource::getImageStatementMonthlyIncome()->columnSpan(12)
                    ->hidden(
                        function(?Model $record):bool{
                            if($record!=null){
                                if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                    $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                    return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'recalculate_monthly_income');
                                }
                            }
                            return false;
                        }
                    )
                    ->disabled(
                        function(?Model $record):bool{
                            if($record!=null){
                                if($record->application_status == Enums\ApplicationStatus::RESUBMISSION_STATUS){
                                    $check_field = new Models\ComponentHelpers\ResubmissionHelper();
                                    return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'recalculate_monthly_income');
                                }
                            }
                            return false;
                        }
                    ),
        ])
        ->columns(6);
    }
}
