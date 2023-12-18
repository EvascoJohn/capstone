<?php

namespace App\Filament\Resources\ReposessionResource\Pages;

use App\Filament\Resources\ReposessionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReposession extends EditRecord
{

    protected static string $resource = ReposessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            
        ];
    }
}
