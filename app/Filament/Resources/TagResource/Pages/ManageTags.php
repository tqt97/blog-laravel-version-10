<?php

namespace App\Filament\Resources\TagResource\Pages;

use App\Filament\Resources\TagResource;
use App\Filament\Resources\TagResource\Widgets\StatsOverview;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Str;

class ManageTags extends ManageRecords
{
    protected static string $resource = TagResource::class;

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
            $this->emit('updateTagStatsOverview');
        }
    }
}
