<?php

namespace App\View\Composers;

use Illuminate\View\View;
use RyanChandler\FilamentNavigation\Facades\FilamentNavigation;


class NavigationComposer
{
    /**
     * Create a new profile composer.
     */
    public function __construct()
    {
    }

    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $mainMenu = FilamentNavigation::get('main-menu');
        $view->with('mainMenu', $mainMenu);
    }
}
