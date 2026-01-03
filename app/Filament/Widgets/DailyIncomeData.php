<?php

namespace App\Filament\Widgets;

use App\Services\LineChartService;
use Filament\Widgets\ChartWidget;

class DailyIncomeData extends ChartWidget
{
  protected static bool $isLazy = false;
  protected bool $isCollapsible = true;
  protected static ?int $sort = 2;
  public function getHeading(): ?string
  {
    return __('Daily Income Data for') . ' ' . now()->monthName;
  }

protected function getData(): array
{
    $stats = app(LineChartService::class)->getDashboardData();
    $lineData = $stats['lineData'];

    return [
        'labels' => $lineData['labels'],
        'datasets' => [
            [
                'label' => __('Income'),
                'data' => $lineData['income_data'],
                'backgroundColor' => 'rgba(34, 197, 94, 0.6)',
                'borderColor' => 'rgb(34, 197, 94)',
                'borderWidth' => 1,
            ],
            [
                'label' => __('Outcome'),
                'data' => $lineData['outcome_data'],
                'backgroundColor' => 'rgba(239, 68, 68, 0.6)', 
                'borderColor' => 'rgb(239, 68, 68)',
                'borderWidth' => 1,
            ],
          
        ],
    ];
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


  protected function getType(): string
  {
    return 'bar';
  }
}
