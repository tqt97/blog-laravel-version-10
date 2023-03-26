<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\Resources\UserResource\RelationManagers\RolesRelationManager;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationGroup = 'Users';

    protected static ?string $recordTitleAttribute = 'name';

    protected static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    protected static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 10 ? 'warning' : 'primary';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Name')
                        ->required()
                        ->maxLength(255)
                        ->autofocus()
                        ->translateLabel(),
                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->unique(ignoreRecord: true)
                        ->required()
                        ->maxLength(255)
                        ->translateLabel(),
                    Forms\Components\DateTimePicker::make('email_verified_at')
                        ->label('Email verified at')
                        ->translateLabel(),
                    Forms\Components\Select::make('roles')
                        ->label('Role')
                        ->multiple()
                        ->relationship('roles', 'name')
                        ->preload()
                        ->searchable()
                        ->translateLabel(),
                    Forms\Components\TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->maxLength(255)
                        ->confirmed()
                        ->required(fn ($livewire) => $livewire instanceof CreateRecord) // the field is only required on the Create page
                        ->dehydrated(fn ($state) => filled($state)) // the field is only saved to the DB when it has a value ($state),
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->translateLabel(),
                    Forms\Components\TextInput::make('password_confirmation')
                        ->label('Password confirmation')
                        ->password()
                        ->maxLength(255)
                        ->translateLabel(),
                    Forms\Components\Toggle::make('is_admin')
                        ->label('Is admin')
                        ->required()
                        ->translateLabel(),
                    Forms\Components\Toggle::make('active')
                        ->label('Active')
                        ->required()
                        ->translateLabel(),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable()->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('email')->sortable()->searchable()->toggleable(),
                Tables\Columns\IconColumn::make('is_admin')->sortable()->searchable()->toggleable()->boolean()
                    ->extraAttributes(['class' => 'flex justify-center'])
                    ->action(function ($record, $column) {
                        $name = $column->getName();
                        $record->update([
                            $name => !$record->$name
                        ]);
                    }),
                Tables\Columns\IconColumn::make('active')->sortable()->searchable()->toggleable()->boolean()
                    ->extraAttributes(['class' => 'flex justify-center'])
                    ->action(function ($record, $column) {
                        $name = $column->getName();
                        $record->update([
                            $name => !$record->$name
                        ]);
                    }),
                Tables\Columns\TextColumn::make('created_at')->sortable()->searchable()->toggleable()->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')->sortable()->searchable()->toggleable()->dateTime(),
                Tables\Columns\TextColumn::make('deleted_at')->sortable()->searchable()->toggleable()->dateTime(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\TernaryFilter::make('active')
                    ->label('Active')
                    ->indicator('Active'),
                Tables\Filters\TernaryFilter::make('is_admin')
                    ->label('Is Admin')
                    ->indicator('Is Admin'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RolesRelationManager::class
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
