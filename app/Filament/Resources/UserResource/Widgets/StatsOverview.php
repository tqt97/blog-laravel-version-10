<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected $listeners = ['updateUserStatOverview' => '$refresh'];

    protected function getCards(): array
    {
        $users = User::select(DB::raw('
            count(*) as total,
            SUM(CASE WHEN active = 1 THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN active = 0 THEN 1 ELSE 0 END) as inactive,
            SUM(CASE WHEN is_admin = 1 THEN 1 ELSE 0 END) as admin
        '))->first();

        return $users->total == 0 ? [] : [
            Card::make(__('Total'), $users->total)
                ->description(__('Total users'))
                ->descriptionIcon('heroicon-s-trending-up')
                ->color('primary'),

            Card::make(__('Active'), $users->active)
                ->description(__('Active user'))
                ->descriptionIcon('heroicon-s-trending-up')
                ->color('success'),

            Card::make(__('Admin'), $users->admin)
                ->description(__('Admin user'))
                ->descriptionIcon('heroicon-s-trending-up')
                ->color('danger'),

            Card::make(__('Inactive'), $users->inactive)
                ->description(__('Inactive user'))
                ->descriptionIcon('heroicon-s-trending-up')
                ->color('secondary'),
        ];
    }
}
