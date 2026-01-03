<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class ProfitChart extends ChartWidget
{
  protected static bool $isLazy = false;
  protected bool $isCollapsible = true;
  protected static ?int $sort = 3;
  public function getHeading(): ?string
  {
    return __('Profit Data');
  }

  protected function getData(): array
  {
    return [
      'datasets' => [
        [
          'label' => 'Orders',
          'data' => [2433, 3454, 4566, 3300, 5545, 5765, 6787, 8767, 7565, 8576, 9686, 8996],
          'fill' => 'start',
        ],
      ],
      'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    ];
  }

  protected function getType(): string
  {
    return 'line';
  }
}
