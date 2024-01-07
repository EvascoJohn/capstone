<?php

namespace App\Filament\Resources;

use App\Enums\ApplicationStatus;
use App\Enums\ReleaseStatus;
use App\Models;
use App\Filament\Resources\UnitReleaseResource\Pages;
use App\Filament\Resources\UnitReleaseResource\RelationManagers;
use App\Models\CustomerApplication;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use App\Filament\Widgets\UnitStocksOverview;
use Filament\Forms\Components\DatePicker;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UnitReleaseResource extends Resource
{
        protected static ?string $model = CustomerApplication::class;

        protected static ?string $navigationLabel = 'Unit Release';

        protected static ?string $label = 'Release';

        protected static ?string $modelLabel = "Release";

        protected ?string $heading = 'Custom Page Heading';

        protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


        protected function getHeaderWidgets(): array
        {
                return [
                        UnitStocksOverview::class,
                ];
        }

        public static function canCreate(): bool
        {
                return false;
        }

        public static function canDelete(Model $record): bool
        {
                return false;
        }

        public static function canEdit(Model $record): bool
        {
                return true;
        }
        public static function form(Form $form): Form
        {
                return $form
                        ->schema([
                                // UnitReleaseResource::getApplicationDetails(),
                                UnitReleaseResource::getUnitInformationComponent()
                                        ->columnSpan(2),
                                UnitReleaseResource::getReleaseDetailsComponent()
                                        ->columns(2)
                                        ->columnSpan(2),
                                UnitReleaseResource::getAvailableUnit()->columnSpan(2),
                                Forms\Components\SpatieMediaLibraryFileUpload::make('media')
                                        ->label('Stencil')
                                        ->columnSpan(2),
                        ]);
        }

        public static function table(Table $table): Table
        {
                return $table
                        ->columns([
                                Tables\Columns\TextColumn::make('id')
                                        ->label("Application ID:")
                                        ->searchable(),
                                Tables\Columns\TextColumn::make('application_type')
                                        ->label("Type:")
                                        ->badge()
                                        ->searchable(),
                                Tables\Columns\TextColumn::make('application_status')
                                        ->label("Status:")
                                        ->badge(),
                                Tables\Columns\TextColumn::make('release_status')
                                        ->label("Release Status:")
                                        ->badge(),
                                Tables\Columns\TextColumn::make('applicant_lastname')
                                        ->label("Last Name:")
                                        ->searchable(),
                                Tables\Columns\TextColumn::make('created_at')
                                        ->label("Date Created:")
                                        ->dateTime('d-M-Y'),
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
                        ->actions([
                                Tables\Actions\EditAction::make()
                                        ->label('Release')
                                        ->hidden(function (?Model $record): bool {
                                                if (
                                                        $record->release_status == ReleaseStatus::UN_RELEASED->value
                                                        && $record->release_status == ApplicationStatus::ACTIVE_STATUS->value
                                                ) {
                                                        return false;
                                                }
                                                return false;
                                        }),
                        ])
                        ->bulkActions([
                                //
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
                        'index' => Pages\ListUnitReleases::route('/'),
                        // 'create' => Pages\CreateUnitRelease::route('/create'),
                        'edit' => Pages\EditUnitRelease::route('/{record}/edit'),
                ];
        }

        //     public static function getEloquentQuery(): Builder
        //     {
        //         return parent::getEloquentQuery()
        //             ->wherehas('payments', function (Builder $query) {
        //                 $twoMonthsAgo = Carbon::now()->subMonths(2);
        //                 $query->where('created_at', '<', $twoMonthsAgo);
        //             });
        //     }


        public static function getAvailableUnit(): Forms\Components\Component
        {
                return Forms\Components\Group::make([
                        Forms\Components\Select::make('search_by')
                                ->options([
                                        'engine_number' => "Engine No.",
                                        'frame_number' => "Frame No.",
                                ])
                                ->live(),
                        Forms\Components\Select::make('units_id')
                                ->live()
                                ->options(
                                        function (Forms\Get $get, ?Model $record): array {
                                                $search_by = $get('search_by');
                                                if ($search_by == null) {
                                                        return [];
                                                }
                                                $units_query = Models\Unit::query()
                                                        ->where([
                                                                'unit_model_id' => $record->unit_model_id,
                                                                'status' => $record->preffered_unit_status,
                                                                'released_status' => ReleaseStatus::UN_RELEASED
                                                        ]);
                                                return $units_query->pluck($search_by, 'id')->toArray();
                                        }
                                )
                                ->prefix('#')
                                ->label('Chasis Number')
                                ->required(true),
                ])->columns(2);
        }

        public static function getApplicationDetails(): Forms\Components\Component
        {
                return Forms\Components\Group::make([
                        Forms\Components\Placeholder::make("preffered_unit_status")
                                ->content(fn (?Model $record): string => $record->preffered_unit_status)
                ]);
        }

        public static function getUnitInformationComponent(): Forms\Components\Component
        {
                return Forms\Components\Group::make([
                        Forms\Components\Fieldset::make('Unit Information')
                                ->schema([
                                        Forms\Components\Hidden::make('id'),
                                        Forms\Components\TextInput::make('unit_model_id')
                                                ->label('Unit Model')
                                                // ->default(fn (?Model $record): string => $record->preffered_unit_status)
                                                ->disabled(),
                                        Forms\Components\TextInput::make('unit_srp')
                                                ->disabled(),
                                ]),
                ]);
        }

        public static function getReleaseDetailsComponent(): Forms\Components\Component
        {
                return Forms\Components\Group::make([
                        Forms\Components\TextInput::make("preffered_unit_status")
                                ->default(fn (?Model $record): string => $record->preffered_unit_status)
                                ->readOnly(),
                        Forms\Components\Select::make('unit_mode_of_payment')
                                ->required(true)
                                ->label('Mode of Payment:')
                                ->options(
                                        ['Office', 'Field', 'Bank',]
                                )
                                ->columnSpan(1),
                ]);
        }
}
