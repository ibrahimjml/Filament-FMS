<?php

namespace App\Filament\Actions;

use BladeUI\Icons\Components\Icon;
use Filament\Actions\Action;


class ClientTypeAction
{
    public static function make(): Action
    {
        return Action::make('create_client_type')
             ->icon('heroicon-o-plus')
            ->label(__('Add Client Type'))
            ->color('success')
            ->modalHeading(__('Add Client Type'))
            ->modalContent(fn () => view('filament.modals.client-type')) 
            ->modalSubmitAction(false) 
            ->modalCancelAction(false);
    }
}
