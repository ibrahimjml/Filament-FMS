<?php

namespace App\Filament\Pages;

use App\Filament\Actions\Events\CreateEventAction;
use App\Models\Event;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Illuminate\View\View;
use UnitEnum;

class Calendar extends Page
{
  protected static string|BackedEnum|null $navigationIcon = Heroicon::Calendar;
  protected static ?int $navigationSort = 3;
  public static function getNavigationGroup(): string|UnitEnum|null
  {
    return __('Schedules');
  }
  public static function getNavigationLabel(): string
  {
    return __('Calendar');
  }
  public $events;
  protected string $view = 'filament.pages.calendar';

  protected function createAction(): Action
  {
    return CreateEventAction::make()
      ->color('warning')
      ->after(fn() =>
      $this->dispatch('refresh_events'));
  }
  protected function viewAction(): ViewAction
  {
    return ViewAction::make('view')
      ->record(fn(array $arguments) => Event::query()->where('event_id', $arguments['id'])->first())
      ->schema([
        Section::make(__('Event name'))
          ->schema([
            TextEntry::make('event_name')
              ->icon('heroicon-o-pencil')
              ->label(__('Event Name')),
          ])->columnSpanFull(),
        Grid::make(2)
          ->schema([
            Section::make(__('Date event'))
              ->schema([
                TextEntry::make('start_date')
                  ->icon('heroicon-o-clock')
                  ->label(__('Start Date')),
                TextEntry::make('end_date')
                  ->icon('heroicon-o-clock')
                  ->label(__('End Date')),
                ColorEntry::make('color')
                  ->label(__('Color')),
                ColorEntry::make('bg_color')
                  ->label(__('Background Color')),
              ])->columnSpanFull()
          ])
      ])
      ->modalHeading(__('Event view'))
      ->extraModalFooterActions(function (array $arguments) {
        return [
          Action::make('edit')
            ->label(__('Edit'))
            ->color('info')
            ->icon('heroicon-o-pencil')
            ->action(function () use ($arguments) {
              $this->replaceMountedAction('editAction', ['id' => $arguments['id']]);
            }),
          Action::make('delete')
            ->label(__('Delete'))
            ->color('danger')
            ->icon('heroicon-o-trash')
            ->action(function () use ($arguments) {
              $this->replaceMountedAction('deleteAction', ['id' => $arguments['id']]);
            })
        ];
      });
  }
  protected function editAction(): Action
  {
    return EditAction::make('editAction')
      ->record(function ($arguments) {
        return Event::query()->where('event_id', $arguments['id'])->first();
      })
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
      ->modalHeading(__('Update Event'))
      ->modalSubmitActionLabel(__('Update'))
      ->modalCancelActionLabel(__('Cancel'))
      ->after(fn() =>
      $this->dispatch('refresh_events'));
  }
  protected function deleteAction(): Action
  {
    return DeleteAction::make('deleteAction')
      ->record(function ($arguments) {
        return Event::query()->where('event_id', $arguments['id'])->first();
      })
      ->label(__('Delete'))
      ->color('danger')
      ->icon('heroicon-o-trash')
      ->after(fn() =>
      $this->dispatch('refresh_events'));
  }
  protected function dropAction(): Action
  {
    return Action::make('dropAction')
      ->action(function ($arguments) {
        $id = $arguments['id'];
        $start = Carbon::parse($arguments['startDate'])->format('Y-m-d');
        $end = Carbon::parse($arguments['endDate'])->subDay()->format('Y-m-d');
        $event = Event::query()->where('event_id', $id)->first();
        $event->start_date = $start;
        $event->end_date = $end;
        $event->save();
      })
      ->successNotification(
        Notification::make()
          ->success()
          ->title(__('event'))
          ->body(__('Event dropped successfully'))
          ->send()
      )
      ->after(fn() =>
      $this->dispatch('refresh_events'));
  }
  public function render(): View
  {
    $events = Event::all()->map(fn($event) => [
      'id'    => $event->event_id,
      'title' => $event->event_name,
      'start' => $event->start_date,
      'end'   => $event->end_date,
      'color' => $event->color,
      'backgroundColor' => $event->bg_color,
    ]);

    $this->events = $events;

    return parent::render();
  }
}
