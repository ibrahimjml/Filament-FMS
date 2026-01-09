<?php

namespace App\Filament\Widgets;


use App\Services\StatsService;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ReportsStats extends StatsOverviewWidget
{
  protected static bool $isLazy = false;

  protected static ?int $sort = 1;

  public ?string $from = null;
  public ?string $to = null;

  protected $listeners = ['setFilters'];

  public function setFilters($data)
  {
    $this->from = $data['from'] ?? null;
    $this->to = $data['to'] ?? null;
  }
  protected function dateLabel(): string
  {
    if ($this->from && $this->to) {
      return Carbon::parse($this->from)->toDateString()
        . ' → ' .
        Carbon::parse($this->to)->toDateString();
    }

    return now()->monthName;
  }
  protected function getStats(): array
  {
    $statsService = app(StatsService::class);
    $data = $statsService->getDashboardData($this->from, $this->to)['stats'];

    return [
      Stat::make(__('Total Paid Payments'), '$ '.number_format($data['total_income']))
        ->description(__('Sum all payments in ') . $this->dateLabel())
        ->descriptionIcon('heroicon-m-arrow-trending-up')
        ->chart($data['incomes_chart'])
        ->color('success'),

      Stat::make(__('Total Outcome'), '$ '.number_format($data['total_outcome']))
        ->description(__('Total expenses in ') . $this->dateLabel())
        ->descriptionIcon('heroicon-m-arrow-trending-down')
        ->color('danger'),

      Stat::make(__('Total Clients'), $data['total_clients'])
        ->description(__('New clients created in ') . $this->dateLabel())
        ->icon('heroicon-m-users')
        ->color('primary'),

      Stat::make(__('Profit'), '$ '.number_format($data['profit']))
        ->description(__('Total profit in ') . $this->dateLabel())
        ->icon('heroicon-m-banknotes')
        ->color(
          $data['profit'] >= 0 ? 'success' : 'danger'
        ),
      Stat::make(__('Total Upcoming Payments'), $data['total_upcoming_payments']),
      Stat::make(__('Total Paid Invoices'), '$ '.number_format($data['total_paid_invoices']))
        ->description(__('Total Paid Invoices in ') . $this->dateLabel())
        ->icon('heroicon-m-receipt-percent')
        ->color('primary'),

    ];
  }
}
