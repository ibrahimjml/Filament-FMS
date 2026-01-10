<?php

namespace App\Filament\Pages;

use BackedEnum;
use App\Models\Event;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Actions\Action;
use App\Filament\Pages\Calendar;
use Filament\Actions\DeleteAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\ColorColumn;
use Filament\Actions\Contracts\HasActions;
use App\Filament\Actions\Events\EditEventAction;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Filament\Actions\Events\CreateEventAction;
use Filament\Actions\Concerns\InteractsWithActions;
use UnitEnum;

class Events extends Page implements HasTable, HasActions
{
  use InteractsWithTable, InteractsWithActions;
  protected static string|BackedEnum|null $navigationIcon = Heroicon::Star;
  protected static ?int $navigationSort = 2;
  public static function getNavigationGroup(): string|UnitEnum|null
  {
    return __('Schedules');
  }
  public static function getNavigationLabel(): string
  {
    return __('Events');
  }
  protected string $view = 'filament.pages.events';

  protected function table(Table $table): Table
  {
    return $table
      ->query(Event::query())
      ->columns([
        TextColumn::make('event_name')
          ->label(__('Event Name')),
        TextColumn::make('start_date')
          ->icon('heroicon-o-clock')
          ->label(__('Start Date')),
        TextColumn::make('end_date')
          ->icon('heroicon-o-clock')
          ->label(__('End Date')),
        ColorColumn::make('color')
          ->label(__('Color')),
        ColorColumn::make('bg_color')
          ->label(__('Background Color')),
      ])
      ->recordActions([
        EditEventAction::make(),

        DeleteAction::make('deleteAction')
          ->hiddenLabel()
          ->icon('heroicon-o-trash')
          ->color('danger'),
      ]);
  }
  protected function createAction(): Action
  {
    return CreateEventAction::make();
  }
  protected function visitCaledar(): Action
  {
    return Action::make('visit')
      ->label(__('Calendar'))
      ->icon('heroicon-o-calendar')
      ->color('warning')
      ->url(fn() => Calendar::getUrl());
  }
}
