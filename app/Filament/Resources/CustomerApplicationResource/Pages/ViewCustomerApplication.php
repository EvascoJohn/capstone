<?php

namespace App\Filament\Resources\CustomerApplicationResource\Pages;

use App\Enums;
use App\Enums\ReleaseStatus;
use Filament\Notifications\Actions\Action;
use Filament\Forms;
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
                    ->slideOver()
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('resubmission_note')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->action(function(array $data){
                        $this->record->setStatusTo(Enums\ApplicationStatus::RESUBMISSION_STATUS);
                        $this->record->resubmission_note = $data["resubmission_note"];
                        $this->record->reject_note = null;
                        Notification::make()
                                ->title('Application is now in resubmission')
                                ->success()
                                ->send();
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
                ->action(function(array $data){
                    $this->record->setStatusTo(Enums\ApplicationStatus::REJECTED_STATUS);
                    $this->record->reject_note = $data["reject_note"];
                    $this->record->resubmission_note = null;
                    $this->record->save();
                    $this->refreshFormData([
                        'application_status',
                    ]);
                    Notification::make()
                    ->title('This application has been rejected!')
                    ->success()
                    ->send();
                })->hidden(
                    function(array $data){
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
    

}
