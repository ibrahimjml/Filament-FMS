<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Actions\ClientTypeAction;
use App\Filament\Actions\EditTypeAction;
use App\Filament\Resources\Clients\ClientResource;
use App\Filament\Resources\Clients\Widgets\ClientsCountWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListClients extends ListRecords
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
               ->label(__('Create Client'))
               ->icon(Heroicon::UserCircle)
               ->button(),
        ClientTypeAction::make(),
        EditTypeAction::make()
        ];
    }
    protected function getHeaderWidgets(): array
    {
      return [
        ClientsCountWidget::class
      ];
    }

}
