<?php

namespace App\Filament\Resources\CommentResource\Widgets;

use App\Models\Comment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected $listeners = ['updateCommentStatsOverview' => '$refresh'];

    protected function getCards(): array
    {
        $comments = Comment::select(DB::raw('count(*) as total '))->first();

        return [
            Card::make(__('Total'), $comments->total)
                ->description(__('Total comments'))
                ->descriptionIcon('heroicon-s-trending-up')
                ->color('primary'),
        ];
    }
}
