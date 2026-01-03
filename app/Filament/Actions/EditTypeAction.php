<?php

namespace App\Filament\Actions;

use BladeUI\Icons\Components\Icon;
use Filament\Actions\Action;


class EditTypeAction
{
  public static function make(): Action
  {
    return Action::make('edit_client_type')
              ->icon('heroicon-o-pencil')
              ->label(__('Edit Client Type'))
              ->color('primary')
              ->modalHeading(__('Edit Client Type'))
              ->modalContent(fn() => view('filament.modals.edit-client-type'))
              ->modalSubmitAction(false)
              ->modalCancelAction(false);
  }
}
