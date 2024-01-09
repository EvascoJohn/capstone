<?php

namespace App\Models\ComponentHelpers;
use Filament\Forms;
use Illuminate\Database\Eloquent\Model;


// ->hidden(
//     function(?Model $record):bool{
//             $check_field = new Models\ComponentHelpers\ResubmissionHelper();
//             return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_fname_textinput');
//     }
// )
// ->disabled(
//     function(?Model $record):bool{
//             $check_field = new Models\ComponentHelpers\ResubmissionHelper();
//             return $check_field->showFieldIfExist($record,Enums\ApplicationSections::APPLICANT->value, 'applicant_fname_textinput');
//     }
// ),

class ResubmissionHelper
{
    public function showSectionIfExist(?Model $record, string $targetSection): bool
    {
        if (!$record) {
            return true;
        }

        $json = $record->resubmissions()->latest('created_at')->first()->getAttributes()['sections_visible'];
        $sections_visible = json_decode($json, true);
        foreach ($sections_visible as $item) {
            if ($item['section'] == $targetSection) {
                return false;
            }
        }
    
        return true;
    }

    public function showFieldIfExist(?Model $record, string $targetSection, string $fieldName): bool
    {
        // if (!$record) {
        //     return false;
        // }

        if($record != null){

            $json = $record->resubmissions()->latest('created_at')->first()->getAttributes()['sections_visible'];
            $sectionsVisible = json_decode($json, true);    

            foreach ($sectionsVisible as $item) {
                if ($item['section'] == $targetSection) {
                    // Check if 'visible_fields' is set in the section
                    if (isset($item['visible_fields']) && is_array($item['visible_fields'])) {
                        // Check if the specified $fieldName is in the 'visible_fields' array
                        return !in_array($fieldName, $item['visible_fields']);
                    }
                }
            }
            return true;
        }
    
        return false; // Default to false if the section or 'visible_fields' is not found
    }

    public function getSectionNote(?Model $record, string $targetSection, string $field): ?string
    {
        if (!$record) {
            return null;
        }
    
        $json = $record->resubmissions()->latest('created_at')->first()->getAttributes()['sections_visible'];
        $sectionsVisible = json_decode($json, true);
    
        foreach ($sectionsVisible as $item) {
            if ($item['section'] == $targetSection && isset($item[$field])) {
                return $item[$field];
            }
        }
    
        return ''; // Default to null if the section or specified $field is not found in the target section
    }    
}