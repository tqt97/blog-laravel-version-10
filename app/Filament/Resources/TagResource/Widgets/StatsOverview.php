<?php

namespace App\Filament\Resources\TagResource\Widgets;

use App\Models\Tag;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected $listeners = ['updateTagStatsOverview' => '$refresh'];


    protected function getCards(): array
    {
        $tags = Tag::select(DB::raw('
            count(*) as total
        '))->first();

        return [
            Card::make(__('Total'), $tags->total)
                ->description(__('Total posts'))
                ->descriptionIcon('heroicon-s-trending-up')
                ->color('primary'),

        ];
    }
}
