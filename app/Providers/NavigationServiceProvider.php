<?php

namespace App\Providers;

use App\View\Composers\NavigationComposer;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use RyanChandler\FilamentNavigation\Facades\FilamentNavigation;

class NavigationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('*', NavigationComposer::class);

        FilamentNavigation::addItemType('Link', [
            TextInput::make('title')->helperText('This is the text that will be displayed in the navigation menu.'),
            TextInput::make('description'),
            TextInput::make('url')->required(),
        ]);

        FilamentNavigation::addItemType('Existing Page', [
            Select::make('page_id')
            ->label('Page')
                ->searchable()
                ->options(function () {
                    return Page::pluck('title', 'id', 'slug');
                })
                ->reactive()
                ->afterStateUpdated(function (callable $set, $state) {
                    if ($state) {
                        $url = Page::whereId($state)->value('slug');
                        $set('url', $url);
                    } else {
                        $set('url', '');
                    }
                }),
            TextInput::make('url')
                ->label('URL')
                ->disabled()
                ->helperText('This URL is automatically generated based on the page you select above.')
                ->hidden(fn (Closure $get) => $get('page_id') === null),
        ]);
    }
}
