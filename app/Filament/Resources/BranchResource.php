<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BanchResource\RelationManagers\UserRelationManager;
use App\Filament\Resources\BranchResource\Pages;
use App\Filament\Resources\BranchResource\RelationManagers;
use App\Models\Branch;
use App\Models\RefBarangay;
use App\Models\RefMunicipality;
use App\Models\RefProvince;
use App\Models\RefRegion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;

    protected static ?string $navigationLabel = 'Branch Management';

    protected static ?string $navigationGroup = 'Management Module';

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Fieldset::make('Address')
                        ->columns(3)
                        ->schema([
                                Forms\Components\Select::make("regCode")
                                        ->columnSpan(1)
                                        ->searchable()
                                        ->preload()
                                        ->relationship('refRegions', 'regDesc')
                                        ->live()
                                        ->label("Region"),
                
                                Forms\Components\Select::make('provCode')
                                        ->columnSpan(1)
                                        ->preload()
                                        ->searchable()
                                        ->relationship('refProvinces', 'provDesc')
                                        ->label("Province")
                                        ->live()
                                        ->options(
                                                function (Forms\Get $get){
                                                        return RefProvince::query()
                                                            ->where('regCode', $get('regCode'))
                                                            ->get()
                                                            ->mapWithKeys(function (RefProvince $province) {
                                                                return [$province->provCode => $province->provDesc];
                                                            });
                                                }
                                        ),

                                Forms\Components\Select::make('citymunCode')
                                        ->columnSpan(1)
                                        ->preload()
                                        ->searchable()
                                        ->relationship('refMunicipalities', 'citymunDesc')
                                        ->label("Municipality")
                                        ->live()
                                        ->options(
                                                function (Forms\Get $get){
                                                        return RefMunicipality::query()
                                                            ->where('provCode', $get('provCode'))
                                                            ->get()
                                                            ->mapWithKeys(function (RefMunicipality $municipality) {
                                                                return [$municipality->citymunCode => $municipality->citymunDesc];
                                                            });
                                                }
                                        ),

                                Forms\Components\Select::make('brgyCode')
                                        ->columnSpan(1)
                                        ->preload()
                                        ->searchable()
                                        ->relationship('refBarangays', 'brgyDesc')
                                        ->label("Barangay")
                                        ->live()
                                        ->options(
                                                function (Forms\Get $get){
                                                        return RefBarangay::query()
                                                            ->where('citymunCode', $get('citymunCode'))
                                                            ->get()
                                                            ->mapWithKeys(function (RefBarangay $barangay) {
                                                                return [$barangay->brgyCode => $barangay->brgyDesc];
                                                            });
                                                }
                                        ),

                                
                                Forms\Components\TextInput::make('street_name')
                                        ->label("Street"),
                                Forms\Components\TextInput::make('full_address')
                                        ->label("Building No."),

                ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make("id")
                        ->wrap()
                        ->label("ID"),
                Tables\Columns\TextColumn::make("full_address")
                        ->label("Full Address"),
                Tables\Columns\TextColumn::make("created_at")
                        ->label("Date Created")
                        ->dateTime('d-M-Y'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            UserRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBranches::route('/'),
            'create' => Pages\CreateBranch::route('/create'),
            'edit' => Pages\EditBranch::route('/{record}/edit'),
        ];
    }    
}
