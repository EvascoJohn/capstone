<?php

namespace App\Filament\Resources\CustomerApplicationResource\Pages;

use App\Enums\ApplicationStatus;
use App\Enums\ReleaseStatus;
use Filament\Forms;
use App\Filament\Resources\CustomerApplicationResource;
use App\Models\Unit;
use App\Models;
use Filament\Actions;
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
                    ->action(function(array $data){
                            $this->record->setStatusTo(ApplicationStatus::APPROVED_STATUS);
                            // $this->record->assignAccount();
                            $this->record->reject_note = null;
                            $this->getRecord()->save(); // saves the record
                    })->hidden(
                        function(array $data){
                                if($this->record->getStatus() == ApplicationStatus::REJECTED_STATUS
                                        || $this->record->getStatus() == ApplicationStatus::APPROVED_STATUS
                                        || $this->record->getStatus() == ApplicationStatus::RESUBMISSION_STATUS)
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
                        $this->record->setStatusTo(ApplicationStatus::RESUBMISSION_STATUS);
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
                                $this->record->getStatus() 
                                    == ApplicationStatus::RESUBMISSION_STATUS) {
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
                    $this->record->setStatusTo(ApplicationStatus::REJECTED_STATUS);
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
                            $this->getRecord()->application_status == ApplicationStatus::REJECTED_STATUS){
                            return true;
                        }
                        return false;
                    }
                );
    }

    // protected function getRepositionButton(): Actions\Action
    // {
    //     return Actions\Action::make("Reposession")
    //             ->color('info')
    //             ->slideOver()
    //             ->action(
    //                 function(array $data){
    //                 $this->record->setStatusTo(ApplicationStatus::REPO_STATUS);
    //                 $this->record->assumed_by_id = $data['assumed_by_id'];
    //                 // $this->record->application_status = ApplicationStatus::CLOSED_STATUS;
    //                 Models\Unit::query()->where('id', $this->record->units->id)->update([
    //                     'status'=> 'repo',
    //                 ]);
    //                 $this->record->release_status = ReleaseStatus::UN_RELEASED;
    //                 $this->getRecord()->save(); // saves the record
    //             })
    //             ->hidden(
    //                 function(array $data){
    //                     if($this->record->getStatus() == ApplicationStatus::REJECTED_STATUS || $this->record->getStatus() == ApplicationStatus::APPROVED_STATUS || $this->record->getStatus() == ApplicationStatus::RESUBMISSION_STATUS)
    //                     {
    //                         return true;
    //                     }
    //                     return false;
    //                 }
    //             )
    //             ->form([
    //                 Forms\Components\Textarea::make('reposession_note')
    //                         ->label('Note'),
    //                 Forms\Components\TextInput::make('assumed_by_firstname')
    //                         ->label('First name'),
    //                 Forms\Components\TextInput::make('assumed_by_middlename')
    //                         ->label('Middle name'),
    //                 Forms\Components\TextInput::make('assumed_by_lastname')
    //                         ->label('Last name'),
    //                 Forms\Components\Select::make('assumed_by_id')
    //                 ->required()
    //                 ->live()
    //                 ->label("Assumed By")
    //                 ->options(
    //                     fn (?Model $record): array => $record::where('application_status', ApplicationStatus::APPROVED_STATUS->value)
    //                             ->limit(20)
    //                             ->pluck('id', 'id')
    //                             ->toArray()
    //                 )
    //                 ->afterStateUpdated(
    //                     function(Forms\Get $get, Forms\Set $set)
    //                     {
    //                         if($get('assumed_by_id') != ""){
    //                             $set('assumed_by_firstname', Models\CustomerApplication::where('id', $get('assumed_by_id'))->first()->applicant_firstname);
    //                             $set('assumed_by_middlename', Models\CustomerApplication::where('id', $get('assumed_by_id'))->first()->applicant_middlename);
    //                             $set('assumed_by_lastname', Models\CustomerApplication::where('id', $get('assumed_by_id'))->first()->applicant_lastname);
    //                         }
    //                         else if($get('assumed_by_id') == ""){
    //                             $set('assumed_by_firstname', "");
    //                             $set('assumed_by_middlename', "");
    //                             $set('assumed_by_lastname', "");
    //                         }
    //                     }
    //                 )
    //             ])->requiresConfirmation();
    // }

    protected function getHeaderActions(): array
    {
        return [
            $this->getApproveButton(),
            $this->getResubmissionButton(),
            // $this->getRepositionButton(),
            $this->getRejectButton(),
        ];
    }
    

}
