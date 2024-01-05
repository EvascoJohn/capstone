<?php

namespace App\Models\ComponentHelpers;
use Filament\Forms;
use Illuminate\Database\Eloquent\Model;

class ResubmissionHelper
{


    public function showSectionIfExist(?Model $record, string $targetSection): bool
    {
        if (!$record) {
            return true;
        }

        $json = $record->resubmissions->getAttributes()['sections_visible'];
        $sections_visible = json_decode($json, true);

        // dd($sections_visible);
    
        foreach ($sections_visible as $item) {
            if ($item['section'] == $targetSection) {
                return false;
            }
        }
    
        return true;
    }
}
