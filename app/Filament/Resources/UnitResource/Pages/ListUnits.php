<?php

namespace App\Filament\Resources\UnitResource\Pages;

use App\Enums\ReleaseStatus;
use App\Filament\Resources\UnitResource;
use App\Models\Unit;
use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListUnits extends ListRecords
{
    protected static string $resource = UnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make('All'),
            'released' => Tab::make()->query(fn ($query) => $query->where('released_status', ReleaseStatus::RELEASED)),
            'unreleased' => Tab::make()->query(fn ($query) => $query->where('released_status', ReleaseStatus::UN_RELEASED)),
        ];
    }
}
