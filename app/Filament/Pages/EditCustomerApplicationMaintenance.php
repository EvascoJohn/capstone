<?php

namespace App\Filament\Pages;

use App\Models\Branch;
use App\Models;
use Filament\Facades\Filament;
use App\Models\CustomerApplicationMaintenance;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Filament\Actions\Action;

class EditCustomerApplicationMaintenance extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $modelLabel = 'Customer Application Maintenance';
    protected static ?string $navigationLabel = 'Customer Application Maintenance';
    protected static ?string $navigationGroup = 'Maintenance Module';
    protected static string $view = 'filament.pages.edit-customer-application';

    public ?array $data = [];


    public function mount()
    {
        $obj = Models\CustomerApplicationMaintenance::first();
        $formatted = Models\DealerhipCalculations::formatMonthlyAmortizationsJson($obj->getAttributes()['monthly_amortizations']);
        $this->form->fill($formatted);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('save'),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
            // Forms\Components\TextInput::make("rebate_value"),
            Forms\Components\Section::make('monthly_amortizations')
                    ->label('Monthly Amortizations')
                    ->description('Customize terms and its corresponding amortization')
                    ->columns(12)
                    ->schema([
                            Forms\Components\Repeater::make('monthly_amortizations')
                                    ->grid(2)
                                    ->columns(12)
                                    ->columnSpan(12)
                                    ->schema([
                                        Forms\Components\TextInput::make('term')
                                                ->columnSpan(6)
                                                ->numeric()
                                                ->suffix('months'),
                                        Forms\Components\TextInput::make('amortization')
                                                ->columnSpan(6)
                                                ->numeric()
                                                ->suffix('interest'),
                                    ]),
                ])
        ])
        ->model(CustomerApplicationMaintenance::class)
        ->statePath('data');
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();

            // Update single row

            $row = CustomerApplicationMaintenance::first();

            if(!$row){
                $row = CustomerApplicationMaintenance::create($data);
            }
            else
            {
                $row->update($data);
            }

        } catch (Halt $exception) {
            return;
        }
    }

}
