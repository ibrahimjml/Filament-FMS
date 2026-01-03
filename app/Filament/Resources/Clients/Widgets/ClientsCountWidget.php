<?php

namespace App\Filament\Resources\Clients\Widgets;

use App\Models\Client;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClientsCountWidget extends StatsOverviewWidget
{
    // re-render poll every 40s
    protected function getPollingInterval(): ?string
    {
        return '40s'; 
    }

    protected function getStats(): array
    {
        return [
            Stat::make(' Deleted Clients', Client::onlyTrashed()->count())
              ->description(__('Total Deleted Clients'))
              ->color('success')
              ->icon('heroicon-o-trash'),
        ];
    }
}
