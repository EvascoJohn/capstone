<?php

namespace App\Filament\Widgets;

use App\Models;
use App\Filament;
use App\Filament\Resources\CustomerApplicationResource;
use App\Filament\Resources\CustomerPaymentAccountResource;
use App\Models\CustomerApplication;
use App\Models\CustomerPaymentAccount;
use Filament\Facades\Filament as FacadesFilament;
use Filament\Forms\Components\DatePicker;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class CustomerDues extends BaseWidget
{

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                function (Builder $query){
                    if(auth()->user()::class == Models\Customer::class){
                        // query by author_id
                        return Models\CustomerPaymentAccount::query()->where('author_id', auth()->user()->id)->latest();
                    }
                    return Models\CustomerPaymentAccount::query()->where('branch_id', auth()->user()->branch_id)->latest();
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
            ])
            ->headerActions([
                ExportAction::make('export')->exports([
                    ExcelExport::make('form')
                        ->askForFilename('Customer-Dues')
                        ->withFilename(fn ($filename) => $filename . '-' . date('M-d-Y'))
                        ->fromTable()
                ])
            ])
            ->filters([
                Filter::make('created_at')
                ->form([
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                        return $query
                        ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                        )
                        ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                        );
                }),
            ])
            ->defaultSort('updated_at', 'desc')
            ->defaultPaginationPageOption(5);
    }

    public function getPages():array
    {
        return [
            'view' => CustomerApplicationResource\Pages\ViewCustomerApplication::route('/{record}'),
        ];
    }

}
