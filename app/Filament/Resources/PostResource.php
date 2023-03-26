<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Filament\Resources\PostResource\RelationManagers\TagsRelationManager;
use App\Models\Post;
use Closure;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Blog';

    protected static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    protected static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 10 ? 'warning' : 'primary';
    }

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(512)
                        ->unique(ignorable: fn ($record) => $record)
                        ->reactive()
                        ->afterStateUpdated(function (Closure $set, $state) {
                            $set('slug', Str::slug($state));
                        })
                        ->autofocus()
                        ->translateLabel(),
                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->unique(ignorable: fn ($record) => $record)
                        ->maxLength(512)
                        ->translateLabel()
                        ->helperText('Use to define unique of post. Default set when change value of name. You can customize it'),
                    Forms\Components\Textarea::make('summary')
                        ->translateLabel()
                        ->hint(function (?string $state) {
                            $readingMinutes = Str::readingMinutes($state);
                            return $readingMinutes . ' min read';
                        })
                        ->lazy(),
                    Forms\Components\RichEditor::make('content')
                        ->translateLabel()
                        ->hint(function (?string $state) {
                            $readingMinutes = Str::readingMinutes($state);
                            return $readingMinutes . ' min read';
                        })
                        ->lazy(),
                ])->columnSpan(8),

                Forms\Components\Card::make()->schema([
                    Forms\Components\FileUpload::make('thumbnail'),
                    Forms\Components\Toggle::make('active')
                        ->translateLabel()
                        ->required()
                        ->onIcon('heroicon-s-lightning-bolt')
                        ->offIcon('heroicon-s-user')
                        ->onColor('success')
                        ->offColor('secondary'),
                    Forms\Components\DateTimePicker::make('published_at')
                        ->translateLabel(),
                    Forms\Components\Select::make('categories')
                        ->translateLabel()
                        ->required()
                        ->multiple()
                        ->relationship('categories', 'name')
                        ->preload()
                        ->searchable(),
                    Forms\Components\Select::make('tags')
                        ->translateLabel()
                        ->multiple()
                        ->relationship('tags', 'name')
                        ->preload()
                        ->searchable(),
                ])->columnSpan(4),
                Forms\Components\Card::make()->schema([
                    Forms\Components\TextInput::make('meta_title')->translateLabel()
                        ->hint(fn ($state, $component) => 'left: ' . $component->getMaxLength() - strlen($state) . ' characters')
                        ->lazy()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('meta_description')->translateLabel()
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
                Tables\Columns\TextColumn::make('title')
                    ->sortable()
                    ->searchable()
                    ->wrap()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('active')
                    ->extraAttributes(['class' => 'flex justify-center'])
                    ->boolean()
                    ->action(function ($record, $column) {
                        $name = $column->getName();
                        $record->update([
                            $name => !$record->$name
                        ]);
                    }),
                Tables\Columns\TextColumn::make('published_at')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->dateTime(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Author')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->dateTime(),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\TernaryFilter::make('active')
                    ->label('Active')
                    ->indicator('Active'),
                // SelectFilter::make('user')->label('Author')->relationship('user', 'name'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\ReplicateAction::make()
                        ->excludeAttributes(['title', 'slug', 'parent_id'])
                        ->form([
                            Forms\Components\TextInput::make('title')->required()
                                ->unique()
                                ->reactive()
                                ->afterStateUpdated(function (Closure $set, $state) {
                                    $set('slug', Str::slug($state));
                                }),
                            Forms\Components\TextInput::make('slug')->translateLabel()
                                ->helperText('Use to define unique of post. Default set when change value of name. You can customize it')
                                ->required()
                                ->unique()
                                ->maxLength(512),
                            Forms\Components\Select::make('categories')
                                ->translateLabel()
                                ->required()
                                ->multiple()
                                ->relationship('categories', 'name')
                                ->preload()
                                ->searchable(),
                        ])
                        ->beforeReplicaSaved(function (Post $replica, array $data): void {
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
        return [
            TagsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
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
