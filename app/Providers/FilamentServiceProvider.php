<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Forms\Components\Field;
use Illuminate\Support\ServiceProvider;
use Filament\Forms\Components\TextInput;
use Filament\Navigation\NavigationGroup;
use Filament\Forms\Components\Actions\Action;

class FilamentServiceProvider extends ServiceProvider
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
        // Reveal on focus TextInputs
        TextInput::macro('revealOnFocus', function () {
            return $this
                ->type('password')
                ->extraInputAttributes([
                    'x-on:focus' => "\$el.type = 'text'",
                    'x-on:blur' => "\$el.type = 'password'",
                ]);
        });
        // Add tooltip
        Field::macro("tooltip", function (string $tooltip) {
            return $this->hintAction(
                Action::make('help')
                    ->icon('heroicon-o-question-mark-circle')
                    ->extraAttributes(["class" => "text-gray-500"])
                    ->label("")
                    ->tooltip($tooltip)
            );
        });
        Filament::serving(function () {
            // Using Vite
            // Filament::registerTheme(
            //     app(Vite::class)('resources/css/filament.css'),
            // );

            // FilamentFabricator::pushMeta([
            //     new HtmlString('<link rel="manifest" href="/site.webmanifest" />'),
            // ]);


            //Register styles
            // FilamentFabricator::registerStyles([
            //     // 'https://unpkg.com/tippy.js@6/dist/tippy.css', //external url
            //     // mix('css/app.css'), //laravel-mix
            //     app(Vite::class)('resources/css/app.css'), //vite
            //     asset('css/app.css'), // asset from public folder
            // ]);

            // FilamentFabricator::favicon(asset('favicon.ico'));
            Filament::registerNavigationGroups([
                'Blog',
                'Settings',
                'Users',
                'Authorization',
            ]);
            // Filament::registerNavigationGroups([
            //     NavigationGroup::make()
            //         ->label('Blog')
            //         ->icon('heroicon-o-book-open')
            //         ->collapsed(),
            //     NavigationGroup::make()
            //         ->label('Users')
            //         ->icon('heroicon-o-users')
            //         ->collapsed(),
            //     NavigationGroup::make('Auth')
            //         ->label('Settings')
            //         ->icon('heroicon-o-cog')
            //         ->collapsed(),
            // ]);
        });
    }
}
