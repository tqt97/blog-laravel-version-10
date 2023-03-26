<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Str::macro('readingMinutes', function ($subject, $wordsPerMinute = 200) {
            return intval(ceil(Str::wordCount(strip_tags($subject)) / $wordsPerMinute));
        });
    }
}
