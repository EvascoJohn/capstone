<?php

namespace App\Filament\Resources\CustomerApplicationResource\Pages;

use App\Enums;
use App\Enums\ReleaseStatus;
use Filament\Notifications\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms;
use Filament\Forms\Components\Select;
use App\Filament\Resources\CustomerApplicationResource;
use App\Models\Unit;
use App\Models;
use App\Filament\TestPanel;
use Filament\Actions;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;

class ViewCustomerApplication extends ViewRecord
{
    protected static string $resource = CustomerApplicationResource::class;

    protected function getApproveButton(): Actions\Action
    {
            return Actions\Action::make("Approve")
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Approve this application?')
                    ->modalDescription("After approving this application a Payment Account will be created.")
                    ->modalSubmitActionLabel('Yes, Approve this Application')
                    ->action(function(array $data, ?Model $record){
                            $this->record->setStatusTo(Enums\ApplicationStatus::APPROVED_STATUS);
                            $this->record->assignAccount();
                            $this->record->reject_note = null;
                            $this->getRecord()->save(); // saves the record
                            // sending two notifications.
                            // checks if the application is walk-in, if so send it only to the employees of the dealerhsip.
                            // checks if the application is online, if so check the author and send it to it.
                            if($record->application_type == Enums\ApplicationType::ONLINE){
                                $customer = Models\Customer::query()->where('id', $record->author_id)->first();
                                Notification::make()
                                        ->title('Application has been approved!')
                                        ->body('An application has been approved.')
                                        ->success()
                                        ->color('success')
                                        ->actions([
                                                Action::make('view')->url(function() use ($record) {
                                                        return TestPanel\Resources\CustomerApplicationResource::getUrl(name:'view', parameters:[$record->id], panel:'customer');
                                                })
                                                ->color('info'),
                                        ])
                                ->sendToDatabase([
                                            $customer
                                ]);
                                event(new DatabaseNotificationsSent($customer));
                            }
                            Notification::make()
                                    ->title('Application has been approved!')
                                    ->body('An application has been approved.')
                                    ->success()
                                    ->color('success')
                                    ->send()
                                    ->actions([
                                            Action::make('view')->url(function () use ($record) {
                                                    return CustomerApplicationResource::getUrl('view', [$record->id]);
                                            })
                                            ->color('info'),
                                    ])
                                    ->sendToDatabase([
                                            auth()->user(),
                                    ]);
                            event(new DatabaseNotificationsSent(auth()->user()));
                    })->hidden(
                        function(){
                                if($this->record->getStatus() == Enums\ApplicationStatus::REJECTED_STATUS
                                        || $this->record->getStatus() == Enums\ApplicationStatus::APPROVED_STATUS
                                        || $this->record->getStatus() == Enums\ApplicationStatus::RESUBMISSION_STATUS)
                                    {
                                    return true;
                                }
                            return false;
                        }
                    );
    }

    protected function getResubmissionButton():Actions\Action
    {
            return Actions\Action::make("Resubmission")
                    // ->slideOver()
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Repeater::make('resubmit_section')
                            ->required()
                            ->schema([
                                    Forms\Components\Select::make('section')
                                            ->options(Enums\ApplicationSections::class)
                                            ->live()
                                            ->afterStateUpdated(fn (Forms\Components\Select $component) => $component
                                                ->getContainer()
                                                ->getComponent('dynamicTypeFields')
                                                ->getChildComponentContainer()
                                                ->fill()),
                                    Forms\Components\Section::make("fields")
                                            ->schema(fn (Forms\Get $get): array => match ($get('section')) {
                                                Enums\ApplicationSections::APPLICANT->value => [
                                                        Forms\Components\Textarea::make('applicant_section_note')
                                                                ->label("Note"),
                                                        Forms\Components\Toggle::make("applicant_info")
                                                                ->label("Applicant's Information"),
                                                        Forms\Components\Toggle::make("co_maker_info")
                                                                ->label("Co-Maker's Information"),
                                                        Forms\Components\Toggle::make("applicant_valid_id")
                                                                ->label("Applicant's ID"),
                                                        Forms\Components\Toggle::make("co_maker_valid_id")
                                                                ->label("Co-Maker's ID"),
                                                ],
                                                Enums\ApplicationSections::EDUCATION->value => [
                                                        Forms\Components\Textarea::make('education_section_note')
                                                                ->label("Note"),
                                                        Forms\Components\Toggle::make("educational_attainment")
                                                                ->label("Educational Attainment"),
                                                        Forms\Components\Toggle::make("dependents")
                                                                ->label("Dependents"),
                                                ],
                                                Enums\ApplicationSections::REFERENCES->value => [
                                                        Forms\Components\Textarea::make('education_section_note')
                                                                ->label("Note"),
                                                        Forms\Components\Toggle::make("educational_attainment")
                                                                ->label("Educational Attainment"),
                                                        Forms\Components\Toggle::make("dependents")
                                                                ->label("Dependents"),
                                                ],
                                                Enums\ApplicationSections::EMPLOYMENT->value => [
                                                        Forms\Components\Textarea::make('education_section_note')
                                                                ->label("Note"),
                                                        Forms\Components\Toggle::make("educational_attainment")
                                                                ->label("Educational Attainment"),
                                                        Forms\Components\Toggle::make("dependents")
                                                                ->label("Dependents"),
                                                ],
                                                Enums\ApplicationSections::MONTHLY_INCOME->value => [
                                                        Forms\Components\Textarea::make('education_section_note')
                                                                ->label("Note"),
                                                        Forms\Components\Toggle::make("educational_attainment")
                                                                ->label("Educational Attainment"),
                                                        Forms\Components\Toggle::make("dependents")
                                                                ->label("Dependents"),
                                                ],
                                                default => [],
                                            })
                                            ->key('dynamicTypeFields')
                            ])
                    ])
                    ->action(function(array $data, ?Model $record){
                        $this->record->setStatusTo(Enums\ApplicationStatus::RESUBMISSION_STATUS);
                        $this->record->reject_note = null;
                        if($record->application_type == Enums\ApplicationType::ONLINE){
                                $customer = Models\Customer::query()->where('id', $record->author_id)->first();
                                Notification::make()
                                        ->title('An application needs resubmission!')
                                        ->body('Updated the application')
                                        ->success()
                                        ->color('success')
                                        ->actions([
                                                Action::make('view')->url(function() use ($record) {
                                                        return TestPanel\Resources\CustomerApplicationResource::getUrl(name:'view', parameters:[$record->id], panel:'customer');
                                                })
                                                ->color('info'),
                                        ])
                                ->sendToDatabase([
                                            $customer
                                ]);
                                event(new DatabaseNotificationsSent($customer));
                            }
                            Notification::make()
                                    ->title('Application is now in Resubmission')
                                    ->body('An application has been set to resubmission')
                                    ->success()
                                    ->color('info')
                                    ->send()
                                    ->actions([
                                            Action::make('view')->url(function () use ($record) {
                                                    return CustomerApplicationResource::getUrl('view', [$record->id]);
                                            })
                                            ->color('info'),
                                    ])
                                    ->sendToDatabase([
                                            auth()->user(),
                                    ]);
                            event(new DatabaseNotificationsSent(auth()->user()));
                        $resubmission = Models\Resubmissions::query()->create(
                            [
                                'sections_visible' => json_encode($data['resubmit_section']),
                                'customer_application_id' => $record->id,
                            ]
                        );
                        $this->record->resubmission_id = $resubmission->id;
                        $resubmission->save();
                        $this->getRecord()->save(); // saves the record
                        $this->refreshFormData([
                            'application_status',
                        ]);
                    })->hidden(
                        function(array $data){
                            if(
                                $this->record->getStatus() == Enums\ApplicationStatus::RESUBMISSION_STATUS ||
                                $this->record->getStatus() == Enums\ApplicationStatus::APPROVED_STATUS ||
                                $this->record->getStatus() == Enums\ApplicationStatus::REJECTED_STATUS
                                ) {
                                return true;
                            }
                            return false;
                        }
                    );
    }

    protected function getRejectButton(): Actions\Action
    {
        return Actions\Action::make("Reject")
                ->color('danger')
                ->slideOver()
                ->requiresConfirmation()
                ->form([
                    Forms\Components\Textarea::make('reject_note')->label('Reason of Rejection:'),
                ])
                ->action(function(array $data, ?Model $record){
                    $this->record->setStatusTo(Enums\ApplicationStatus::REJECTED_STATUS);
                    $this->record->reject_note = $data["reject_note"];
                    $this->record->resubmission_note = null;
                    $this->record->save();
                    $this->refreshFormData([
                            'application_status',
                    ]);
                    if($record->application_type == Enums\ApplicationType::ONLINE){
                        $customer = Models\Customer::query()->where('id', $record->author_id)->first();
                        Notification::make()
                                ->title('Application has been rejected!')
                                ->body('An application has been rejected.')
                                ->danger()
                                ->color('danger')
                                ->actions([
                                        Action::make('view')->url(function() use ($record) {
                                                return TestPanel\Resources\CustomerApplicationResource::getUrl(name:'view', parameters:[$record->id], panel:'customer');
                                        })
                                        ->color('info'),
                                ])
                        ->sendToDatabase([
                                    $customer
                        ]);
                        event(new DatabaseNotificationsSent($customer));
                    }
                    Notification::make()
                            ->title('Application has been rejected!')
                            ->body('An application has been rejected.')
                            ->danger()
                            ->color('danger')
                            ->send()
                            ->actions([
                                    Action::make('view')->url(function () use ($record) {
                                            return CustomerApplicationResource::getUrl('view', [$record->id]);
                                    })
                                    ->color('info'),
                            ])
                            ->sendToDatabase([
                                    auth()->user(),
                            ]);
                    event(new DatabaseNotificationsSent(auth()->user()));
                })->hidden(
                    function(){
                        if(
                                $this->record->getStatus() == Enums\ApplicationStatus::RESUBMISSION_STATUS ||
                                $this->record->getStatus() == Enums\ApplicationStatus::APPROVED_STATUS ||
                                $this->record->getStatus() == Enums\ApplicationStatus::REJECTED_STATUS
                            ){
                            return true;
                        }
                        return false;
                    }
                );
    }
    protected function getHeaderActions(): array
    {
        return [
            $this->getApproveButton(),
            $this->getResubmissionButton(),
            $this->getRejectButton(),
        ];
    }

    // Components used above

    public static function getSectionApplicantInformation(): Forms\Components\Component
    {
        return Forms\Components\Group::make([
                Forms\Components\Toggle::make("Applicant First Name"),
                Forms\Components\Toggle::make("Applicant Last Name"),
                Forms\Components\Toggle::make("Applicant Contact No."),
                Forms\Components\Toggle::make("Applicant Email"),
                Forms\Components\Toggle::make("Applicant Valid ID's")
        ]);
    }
}
