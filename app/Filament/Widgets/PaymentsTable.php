<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Forms\Components\DatePicker;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class PaymentsTable extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                function (){
                    return Payment::query()->latest();
                }
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID'),
                Tables\Columns\TextColumn::make('amount_to_be_paid')
                        ->summarize(Sum::make()->label('Total')),
                Tables\Columns\TextColumn::make('payment_is')
                    ->badge(),
        ])
        ->headerActions([
            ExportAction::make('export')->exports([
                ExcelExport::make('form')
                    ->askForFilename('Unit-Stocks')
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
            SelectFilter::make('payment_is')
                ->options([
                    'monthly' => 'Monthly',
                    'down payment' => 'Downpayment',
                    'cash' => 'Cash',
                ])
        ])
        ->defaultSort('updated_at', 'desc')
        ->defaultPaginationPageOption(5);
    }
}
