<?php

namespace App\Traits;

use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

trait ExportToExcelTrait {
    
    public function export(?string $defaultFileName = '')
    {
        return ExportAction::make('export')->exports([
            ExcelExport::make('form')
                ->askForFilename($defaultFileName)
                ->withFilename(fn ($filename) => $filename . '-' . date('M-d-Y'))
                ->fromTable()
        ]);
    }

}