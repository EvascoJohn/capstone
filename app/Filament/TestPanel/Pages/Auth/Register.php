<?php

namespace App\Filament\TestPanel\Pages\Auth;

use Filament\Actions\Action;
use Filament\Actions\Concerns\HasInfolist;
use Filament\Forms\Components\Component;
use Filament\Pages\Auth\Register as AuthRegister;
use Filament\Forms;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Infolists;
use Filament\Resources\Pages\Page;


class Register extends AuthRegister
{
    use InteractsWithFormActions;
    // 'resources\views\filament\test-panel\pages\auth\register.blade.php'
    protected static string $view = 'filament.test-panel.pages.auth.register';
    
    protected function getForms():array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        // $this->getPersonalInformationComponent(),
                        $this->openTermsAndConditionsComponent(),
                        $this->getTermsAndConditionComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getTermsAndConditionComponent(): Component
    {
        return Forms\Components\Group::make()->schema([
            Forms\Components\Checkbox::make('terms-and-conditions')
                    ->label('I have read the Terms and Conditions')
                    ->accepted()
                    ->required(),
        ]);
    }

    protected function getNameComponent(): Component
    {
        return Forms\Components\Group::make()->schema([
            Forms\Components\TextInput::make('firstname')
                    ->label('First name')
                    ->required(),
            Forms\Components\TextInput::make('lastname')
                    ->label('Last name')
                    ->required(),
        ]);
    }

    protected function getPersonalInformationComponent(): Component
    {
        return Forms\Components\Group::make()->schema([
            Forms\Components\TextInput::make('contact_number')
                    ->numeric()
                    ->required(),
            Forms\Components\DatePicker::make('birthday')
            
        ]);
    }

    public function registerAction(): Action
    {
        return Action::make('register')
            ->submit('register');
    }

    public function openTermsAndConditionsComponent(): Component
    {
        return Forms\Components\Actions::make([
            Forms\Components\Actions\Action::make('terms_and_conditions')
            // resources\views\components\filament-fabricator\page-blocks\customer-terms-and-conditions.blade.php
                ->url(route('terms-and-conditions'), true)
                ->link()
        ]);
    }

}
