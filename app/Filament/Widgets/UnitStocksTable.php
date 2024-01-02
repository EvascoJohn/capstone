<?php

namespace App\Filament\Widgets;

use App\Models\Unit;
use Filament\Forms\Components\DatePicker;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class UnitStocksTable extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                function (){
                    return Unit::query()->latest();
                }
            )
            ->columns([
                TextColumn::make('id')->label('Id')->toggledHiddenByDefault(),
                TextColumn::make('unitModel.model_name')->label('Model'),
                TextColumn::make('Stocks')
                    ->getStateUsing( function (Model $record){
                        return Unit::where('unit_model_id', $record->id)
                                ->whereNull('customer_application_id')
                                ->count();
                    }),
                TextColumn::make('status')->label('status')
                    ->badge(),
                TextColumn::make('created_at')->toggledHiddenByDefault(),
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
                SelectFilter::make('status')
                ->options([
                    'Reposession' => 'Reposession',
                    'Brand new' => 'Brand New',
                    'Depo' => 'Depo',
                ])
            ])
            ->defaultSort('updated_at', 'desc')
            ->defaultPaginationPageOption(5);
    }
}
