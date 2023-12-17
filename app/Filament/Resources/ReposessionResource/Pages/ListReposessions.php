<?php

namespace App\Filament\Resources\ReposessionResource\Pages;

use App\Enums\ApplicationStatus;
use App\Filament\Resources\ReposessionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReposessions extends ListRecords
{
    protected static string $resource = ReposessionResource::class;

    public function getTabs(): array
    {
        return [
            // Taking this query.
            null => ListRecords\Tab::make()->query(fn ($query) => $query->where('application_status', ApplicationStatus::REPO_STATUS->value)),
        ];
    }

}
