<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Incomes\IncomeResource;
use App\Filament\Resources\Outcomes\OutcomesResource;
use App\Filament\Widgets\ReportsStats;
use App\Models\Income;
use App\Models\Outcome;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Tables\Table;
use Carbon\Carbon;

class Reports extends Page implements HasForms, HasTable
{
  use InteractsWithForms, InteractsWithTable;

  protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';
  protected static ?string $navigationLabel = 'Reports';
  protected string $view = 'filament.pages.reports';

  public ?string $from = null;
  public ?string $to = null;

  public function mount(): void
  {
  
    $this->from = null;
    $this->to = null;
  }
  protected function getFormSchema(): array
  {
    return [
      DatePicker::make('from')
        ->label(__('From')),

      DatePicker::make('to')
        ->label(__('To')),
    ];
  }
  protected function getTables(): array
  {
    return [
      'income' => $this->incomeTable(),
      'outcome' => $this->outcomeTable(),
    ];
  }
  protected function incomeTable(): Table
  {
    if ($this->from && $this->to) {
      $start = Carbon::parse($this->from)->startOfDay();
      $end = Carbon::parse($this->to)->endOfDay();

      return IncomeResource::table(Table::make($this))
        ->query(Income::query()->whereBetween('created_at', [$start, $end]));
    }

    return IncomeResource::table(Table::make($this))->query(Income::query())
                ->recordActions([
                    Action::make('view')
                        ->url(fn(Income $record):string => IncomeResource::getUrl('view',['record'=>$record->income_id]))
                        ->label(__('View'))
                        ->icon('heroicon-o-eye')
                        ->color('gray')
                ]);
  }

  protected function outcomeTable(): Table
  {
    if ($this->from && $this->to) {
      $start = Carbon::parse($this->from)->startOfDay();
      $end = Carbon::parse($this->to)->endOfDay();

      return OutcomesResource::table(Table::make($this))
        ->query(Outcome::query()->whereBetween('created_at', [$start, $end]));
    }

    return OutcomesResource::table(Table::make($this))->query(Outcome::query());
  }

  public function renderTable(string $name): ?Htmlable
  {
    $tables = $this->getTables();

    if (! isset($tables[$name])) {
      return null;
    }

    $this->table = $tables[$name];

    $this->cachedTableRecords = null;
    $this->resetPage();

    if ($this->getTable()->isPaginated()) {
      $this->tableRecordsPerPage = $this->getDefaultTableRecordsPerPageSelectOption();
    }

    return $this->getTable()->toHtmlString();
  }
  protected function getHeaderActions(): array
  {
    return [
      Action::make('filters')
        ->label(__('Filters'))
        ->icon('heroicon-o-funnel')
        ->modalHeading(__('Filters'))
        ->modalSubmitActionLabel(__('Apply'))
        ->modalCancelActionLabel(__('Cancel'))
        ->schema(fn() => $this->getFormSchema())
        ->action(function (array $data) {
          $this->from = $data['from'] ?? $this->from;
          $this->to   = $data['to'] ?? $this->to;
          $this->dispatch('setFilters', ['from' => $this->from, 'to' => $this->to])->to(ReportsStats::class);
        }),

      Action::make('clearFilters')
        ->label(__('Clear'))
        ->icon('heroicon-o-x-mark')
        ->color('gray')
        ->visible(function () {
          return $this->from && $this->to;
        })
        ->action(function () {
          $this->from = null;
          $this->to   = null;
          $this->dispatch('setFilters', ['from' => null, 'to' => null])->to(ReportsStats::class);
        }),
    ];
  }
  protected function getHeaderWidgets(): array
{
    return [
        ReportsStats::class,
    ];
}
}
