<?php

namespace App\Filament\Widgets;

use App\Services\LineChartService;
use Filament\Widgets\ChartWidget;

class ProfitChart extends ChartWidget
{
  protected static bool $isLazy = false;
  protected bool $isCollapsible = true;
  protected static ?int $sort = 3;
  public function getHeading(): ?string
  {
    return __('Profit Data for') . ' ' . now()->monthName;
  }

  protected function getData(): array
  {
    $profit = app(LineChartService::class)->getDashboardData();
    $profitData = $profit['profitData'];

    return [
      'datasets' => [
        [
          'label' => __('Profit') . ' (' . __('Total Paid') . ')',
          'data' => $profitData['profit'],
          'backgroundColor' => 'rgba(34, 197, 94, 0.6)',
          'fill' => false,
        

        ],
      ],
      'labels' => $profitData['labels'],
    ];
  }

  protected function getType(): string
  {
    return 'line';
  }
protected function getOptions(): array
{
    return [
        'responsive' => true,
        'maintainAspectRatio' => false,
        'plugins' => [
            'legend' => [
                'position' => 'top',
            ],
        ],
    ];
}
}
