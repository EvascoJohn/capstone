<?php

namespace App\Filament\Widgets;

use App\Models;
use App\Filament;
use App\Filament\Resources\CustomerApplicationResource;
use App\Filament\Resources\CustomerPaymentAccountResource;
use App\Models\CustomerApplication;
use App\Models\CustomerPaymentAccount;
use Filament\Facades\Filament as FacadesFilament;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class CustomerDues extends BaseWidget
{

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                function (Builder $query){
                    return Models\CustomerPaymentAccount::query()->where('due_date', '!=', 'null')->latest();
                }
            )
            ->columns([
                Tables\Columns\TextColumn::make("id")
                        ->label("Account ID"),
                Tables\Columns\TextColumn::make("due_date"),
                Tables\Columns\TextColumn::make("customerApplication.applicant_full_name"),
                Tables\Columns\TextColumn::make("monthly_payment")
                        ->label("Monthly Amort.")
                        ->money("php")
                        ->color("success"),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn (CustomerPaymentAccount $record): string => CustomerPaymentAccountResource::getUrl('edit', ['record' => $record])),
            ]);
    }

    public function getPages():array
    {
        return [
            'view' => CustomerApplicationResource\Pages\ViewCustomerApplication::route('/{record}'),
        ];
    }

}
