<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnitModelResource\Pages;
use App\Filament\Resources\UnitModelResource\RelationManagers;
use App\Models\Branch;
use App\Enums;
use App\Models\Unit;
use App\Models\UnitModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\RawJs;

class UnitModelResource extends Resource
{
    protected static ?string $model = UnitModel::class;

    protected static ?string $navigationGroup = 'Inventory Module';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getUnitModelDetails(): Forms\Components\Component
    {
        return Forms\Components\Group::make([
            Forms\Components\Select::make('body_type')
                ->columnSpan(1)
                ->options(Enums\UnitTypes::class)
                ->required(),
            Forms\Components\Select::make('engine_type')
                ->options(Enums\EngineTypes::class)
                ->required()
                ->columnSpan(1),
            Forms\Components\TextInput::make('displacement')
                ->mask(Rawjs::make(<<<'JS'
                    '9999999'
                JS))
                ->suffix('cc')
                ->required()
                ->columnSpan(1),
            Forms\Components\TextInput::make('engine_oil')
                ->numeric()
                ->suffix('L')
                ->required()
                ->columnSpan(1),
            Forms\Components\Select::make('starting_system')
                ->options(Enums\StartingSystemTypes::class)
                ->required()
                ->columnSpan(1),
            Forms\Components\Select::make('transmission')
                ->options(Enums\TransmissionTypes::class)
                ->required()
                ->columnSpan(1),
            Forms\Components\TextInput::make('fuel_tank_capacity')
                ->inputMode('integer')
                ->required()
                ->columnSpan(1),
            Forms\Components\TextInput::make('net_weight')
                ->inputMode('decimal')
                ->suffix('kg')
                ->required()
                ->columnSpan(1),
            Forms\Components\TextInput::make('dimension')
                ->maxLength(50)
                ->required()
                ->columnSpan(1),
            Forms\Components\Select::make('colors')
                ->multiple()
                ->options(Enums\UnitColors::class)
                ->required()
                ->columnSpan(1),
            Forms\Components\MarkdownEditor::make('description')
                ->maxLength(255)
                ->columnSpan(2)
                ->required()
                ->toolbarButtons([]),
        ])
        ->columns(3);
    }

    public static function getImportantDetailsComponent(): Forms\Components\Component
    {
        return Forms\Components\Group::make([
                Forms\Components\FileUpload::make('image_file')
                    ->columnSpan(3)
                    ->directory('unit_model_images')
                    ->acceptedFileTypes(['image/png','image/jpg'])
                    ->disk('public'),
                Forms\Components\TextInput::make('model_name')
                    ->maxLength(255)
                    ->required()
                    ->columnSpan(2),
                Forms\Components\TextInput::make('price')
                    ->inputMode('decimal')
                    ->numeric(true)
                    ->required()
                    ->columnSpan(1),
                Forms\Components\TextInput::make('down_payment_amount')
                    ->inputMode('decimal')
                    ->numeric(true)
                    ->required()
                    ->columnSpan(1),
        ]);
    }



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                static::getImportantDetailsComponent()->columnSpan(2),
                Forms\Components\Placeholder::make('branch')
                ->columns(1)
                ->label('Current Branch')
                ->content(fn ():string => Branch::query()
                ->where('id', auth()->user()->branch_id)->first()->full_address),
                static::getUnitModelDetails()->columnSpan(2),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_file')->disk('public'),
                Tables\Columns\TextColumn::make('model_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('php')
                    ->searchable(),
                Tables\Columns\TextColumn::make('body_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('Stock')
                    ->getStateUsing( function (Model $record){
                        return Unit::where('unit_model_id', $record->id)
                                ->whereNull('customer_application_id')
                                ->count();
                    })
                    ->label('Unit Quantity')
                    ->searchable(),
                    // ->counts('unit')

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->action(
                        function(Model $record){
                            if($record->getMedia('product-images')->first()){
                                $record->getMedia('product-images')->first()->delete();
                            }
                            $record->delete();
                        }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUnitModels::route('/'),
            'create' => Pages\CreateUnitModel::route('/create'),
            'edit' => Pages\EditUnitModel::route('/{record}/edit'),
        ];
    }    
}
