<?php

namespace App\Filament\Actions\Events;

use App\Models\Event;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;

class EditEventAction
{
  public static function make(): Action
  {
    return Action::make('editAction')
      ->hiddenLabel()
      ->icon('heroicon-o-pencil')
      ->color('info')
      ->fillForm(fn(Event $record) => [
        'event_name' => $record->event_name,
        'start_date' => $record->start_date,
        'end_date'   => $record->end_date,
        'color'      => $record->color,
        'bg_color'   => $record->bg_color,
      ])
      ->schema([
        Fieldset::make(__('Event Name'))
          ->schema([
            TextInput::make('event_name')
              ->label(__('Event Name')),
          ])->columnSpanFull(),
        Grid::make(2)
          ->schema([
            Fieldset::make(__('Date event'))
              ->schema([
                DatePicker::make('start_date')
                  ->date()
                  ->label(__('Start Date')),
                DatePicker::make('end_date')
                  ->date()
                  ->label(__('End Date')),
              ])->columns(1),
            Fieldset::make(__('Color event'))
              ->schema([
                ColorPicker::make('color')
                  ->nullable()
                  ->label(__('Color')),
                ColorPicker::make('bg_color')
                  ->nullable()
                  ->label(__('Background Color')),
              ])->columns(1)
          ])
      ])
      ->modalHeading(__('Update Event'))
      ->modalSubmitActionLabel(__('Update'))
      ->modalCancelActionLabel(__('Cancel'))
      ->action(function (array $data, Event $record) {
        $record->update($data);
      })
      ->successNotification(function () {
        return Notification::make()
          ->success()
          ->title(__('event'))
          ->body(__('Event updated successfully'))
          ->send();
      });
  }
}
