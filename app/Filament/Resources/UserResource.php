<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'User Management';

    protected static ?string $navigationGroup = 'Management Module';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                        ->columnSpan(1)
                        ->columns(2)
                        ->schema([

                            Forms\Components\TextInput::make('id')
                                    ->label("User ID")
                                    ->columnSpan(2)
                                    ->disabled(true)
                                    ->columnSpan(1),

                            Forms\Components\Fieldset::make()
                                    ->label("User Personal Information")
                                    ->columnSpan(2)
                                    ->columns(2)
                                    ->schema([
                                            Forms\Components\TextInput::make('firstname')
                                                    ->columnSpan(2)
                                                    ->label("First name")
                                                    ->required()
                                                    ->maxLength(255),
                                            Forms\Components\TextInput::make('lastname')
                                                    ->columnSpan(2)
                                                    ->label("Last name")
                                                    ->required()
                                                    ->maxLength(255),
                                            Forms\Components\Select::make('gender')
                                                    ->label("Gender")
                                                    ->required()
                                                    ->options([
                                                            "male" => "Male",
                                                            "female" => "Female",
                                                    ]),
                                            Forms\Components\DatePicker::make("birthday")
                                                    ->required()
                                                    ->label("Birthday")
                                                    ->maxDate(now())
                                                    ->minDate(now()->subYears(100)),
                                            Forms\Components\TextInput::make('password')
                                                    ->hidden(fn (string $operation): string => $operation == "edit")
                                                    ->password()
                                                    ->label("Password")
                                                    ->required(),
                                            Forms\Components\TextInput::make("email")
                                                    ->required()
                                                    ->label("Email")
                                                    ->email()
                                                    ->maxLength(255),
                                            Forms\Components\TextInput::make("contact_number")
                                                    ->required()
                                                    ->label("Contact No.")
                                                    ->tel()
                                                    ->maxLength(255),
                                    ]),
                        ]),

                        Forms\Components\Group::make()
                                ->label("Account Details")
                                ->columns(1)
                                ->schema([
                                        Forms\Components\Select::make("roles")
                                                ->required()
                                                ->relationship('roles', 'name')
                                                ->preload(),
                                        Forms\Components\Select::make("branch_id")
                                                ->required()
                                                ->relationship('branch', 'id')
                                                ->searchable()
                                                ->preload(),
                                        Forms\Components\Toggle::make('is_admin')
                                                ->required()
                                                ->label("Admin"),
                                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_admin')
                ->options([
                    false => 'heroicon-o-x-circle',
                    true => 'heroicon-o-check-circle',
                ])
                    ->colors([
                        false => 'secondary',
                        true => 'success',
                    ])
                    ->label("Admin")
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
            
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }    
}
