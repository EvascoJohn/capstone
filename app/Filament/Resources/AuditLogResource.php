<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditLogResource\Pages;
use App\Filament\Resources\AuditLogResource\RelationManagers;
use App\Models\AuditLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;
    
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }
    
    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Utilities';
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([

                Infolists\Components\Section::make("Audit Details")
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('user.name')
                            ->label("Auditor")
                            ->columnSpan(1),
                    Infolists\Components\TextEntry::make('operation')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'created' => 'success',
                                'update' => 'success',
                                'deleted' => 'warning',
                            }),
                    Infolists\Components\TextEntry::make('created_at'),
                ])->columns(3),

                Infolists\Components\Section::make("Record Details")
                        ->columns(2)
                        ->schema([
                                Infolists\Components\TextEntry::make('model')
                                        ->columnSpan(1),
                                Infolists\Components\TextEntry::make('record_id')
                                        ->columnSpan(1),
                        ]),

                Infolists\Components\Section::make("Changes")
                        ->columns(2)
                        ->schema([
                                Infolists\Components\TextEntry::make('new_details')
                                        ->columnSpan(1),
                                Infolists\Components\TextEntry::make('old_details')
                                        ->columnSpan(1),
                        ]),
            ])->columns(1);
    }
    

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('user.name'),
                Tables\Columns\TextColumn::make('created_at'),
                Tables\Columns\TextColumn::make('operation'),
                Tables\Columns\TextColumn::make('record_id'),
                Tables\Columns\TextColumn::make('model'),
                Tables\Columns\TextColumn::make('new_details'),
                Tables\Columns\TextColumn::make('old_details')
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListAuditLogs::route('/'),
            // 'create' => Pages\CreateAuditLog::route('/create'),
            // 'edit' => Pages\EditAuditLog::route('/{record}/edit'),
        ];
    }    
}
