<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use App\Filament\Resources\CategoryResource\Widgets\StatsOverview;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Str;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            StatsOverview::class,
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverview::class,
        ];
    }
    public function updated($name)
    {
        if (Str::of($name)->contains(['mountedTableAction', 'mountedTableBulkAction'])) {
            $this->emit('updateCategoryStatsOverview');
        }
    }
}
