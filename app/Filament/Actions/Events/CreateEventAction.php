<?php

namespace App\Filament\Actions\Events;

use App\Models\Event;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;

class CreateEventAction
{
  public static function make(): Action
  {
    return Action::make('createAction')
      ->label(__('Create Event'))
      ->icon('heroicon-o-plus')
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
      ->modalHeading(__('Create Event'))
      ->modalSubmitActionLabel(__('Create'))
      ->modalCancelActionLabel(__('Cancel'))
      ->action(function (array $data) {
        Event::create($data);
      });
  }
}
