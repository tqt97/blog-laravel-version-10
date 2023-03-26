<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationGroup = 'Blog';

    protected static ?string $recordTitleAttribute = 'name';

    protected static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    protected static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 10 ? 'warning' : 'primary';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\TextInput::make('name')->label(__('Name'))
                        ->autofocus()
                        ->translateLabel()
                        ->minLength(3)
                        ->maxLength(255)
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function (Closure $set, $state) {
                            $set('slug', Str::slug($state));
                        })
                        ->unique(ignorable: fn ($record) => $record),
                    Forms\Components\TextInput::make('slug')
                        ->translateLabel()
                        ->helperText('Use to define unique of category. Default set when change value of name. You can customize it')
                        ->required()
                        ->maxLength(512)
                        ->unique(ignorable: fn ($record) => $record),
                    Forms\Components\Textarea::make('description')->label('Description')
                        ->translateLabel()
                        ->hint(function (?string $state) {
                            $readingMinutes = Str::readingMinutes($state);

                            return $readingMinutes . ' min read';
                        })
                        ->lazy(),
                ])->columnSpan(8),
                Forms\Components\Card::make()->schema([
                    Forms\Components\Select::make('parent_id')
                        ->label('Categories')
                        ->translateLabel()
                        ->helperText('Default is null. It\'s root category.')
                        ->relationship('parent', 'name')
                        ->searchable()
                        // ->default(
                        //     Category::query()
                        //         ->latest('created_at')
                        //         ->pluck('category_id') ?
                        //         Category::query()
                        //         ->latest('created_at')
                        //         ->pluck('category_id')
                        //         ->first() :
                        //         null
                        // )
                        ->preload(),
                    Forms\Components\FileUpload::make('banner')
                        ->translateLabel()
                        ->helperText('Default size image is 1200px x 600px.')
                        ->preserveFilenames()
                        ->image()
                        ->loadingIndicatorPosition('left')
                        ->removeUploadedFileButtonPosition('right')
                        ->uploadButtonPosition('left')
                        ->uploadProgressIndicatorPosition('left'),
                    Forms\Components\Toggle::make('active')
                        ->translateLabel()
                        ->onIcon('heroicon-s-lightning-bolt')
                        ->offIcon('heroicon-s-lightning-bolt')
                        ->required(),
                ])->columnSpan(4),
                Forms\Components\Card::make()->schema([
                    Forms\Components\TextInput::make('meta_title')
                        ->translateLabel()
                        ->hint(fn ($state, $component) => 'left: ' . $component->getMaxLength() - strlen($state) . ' characters')
                        ->lazy()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('meta_description')
                        ->translateLabel()
                        ->hint(fn ($state, $component) => 'left: ' . $component->getMaxLength() - strlen($state) . ' characters')
                        ->lazy()
                        ->maxLength(255),
                ])->columnSpan(12),
            ])->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Categories Root')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('active')
                    ->sortable()->searchable()->toggleable()->boolean()
                    ->extraAttributes(['class' => 'flex justify-center'])
                    ->action(function ($record, $column) {
                        $name = $column->getName();
                        $record->update([
                            $name => !$record->$name
                        ]);
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()->searchable()->toggleable()->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->sortable()->searchable()->toggleable()->dateTime(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->sortable()->searchable()->toggleable()->dateTime(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\TernaryFilter::make('active')
                    ->label('Active')
                    ->indicator('Active'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\ReplicateAction::make()
                        ->excludeAttributes(['name', 'slug'])
                        ->form([
                            TextInput::make('name')->required()
                                ->unique()
                                ->reactive()
                                ->afterStateUpdated(function (Closure $set, $state) {
                                    $set('slug', Str::slug($state));
                                }),
                            Forms\Components\TextInput::make('slug')->translateLabel()
                                ->helperText('Use to define unique of category. Default set when change value of name. You can customize it')
                                ->required()
                                ->unique()
                                ->maxLength(512),
                        ])
                        ->beforeReplicaSaved(function (Category $replica, array $data): void {
                            $replica->fill($data);
                        }),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
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
