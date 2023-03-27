<?php

namespace App\Filament\Resources\CategoryResource\Widgets;

use App\Models\Category;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected $listeners = ['updateCategoryStatsOverview' => '$refresh'];

    protected function getCards(): array
    {
        $categories = Category::select(DB::raw('
            count(*) as total,
            SUM(CASE WHEN active = 1 THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN active = 0 THEN 1 ELSE 0 END) as inactive
        '))->first();

        return $categories->total == 0 ? [] : [
            Card::make(__('Total categories'), $categories->total)
                ->description(__('Total categories'))
                ->descriptionIcon('heroicon-s-trending-up')
                ->color('primary'),

            Card::make(__('Active categories'), $categories->active)
                ->description(__('Active categories'))
                ->descriptionIcon('heroicon-s-trending-up')
                ->color('success'),

            Card::make(__('Inactive categories'), $categories->inactive)
                ->description(__('Inactive categories'))
                ->descriptionIcon('heroicon-s-trending-up')
                ->color('secondary'),
        ];
    }
}
