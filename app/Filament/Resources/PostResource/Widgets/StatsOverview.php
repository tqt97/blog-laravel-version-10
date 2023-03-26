<?php

namespace App\Filament\Resources\PostResource\Widgets;

use App\Models\Post;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected $listeners = ['updatePostStatsOverview' => '$refresh'];

    protected function getCards(): array
    {
        $posts = Post::select(DB::raw('
            count(*) as total,
            SUM(CASE WHEN active = 1 AND published_at != "" THEN 1 ELSE 0 END) as publish,
            SUM(CASE WHEN active = 0 THEN 1 ELSE 0 END) as unpublish
        '))->first();

        return [
            Card::make(__('Total'), $posts->total)
                ->description(__('Total posts'))
                ->descriptionIcon('heroicon-s-trending-up')
                ->color('primary'),

            Card::make(__('Publish'), $posts->publish)
                ->description(__('Publish posts'))
                ->descriptionIcon('heroicon-s-trending-up')
                ->color('success'),

            Card::make(__('Unpublish'), $posts->unpublish)
                ->description(__('Unpublish post'))
                ->descriptionIcon('heroicon-s-trending-up')
                ->color('secondary'),
        ];
    }
}
