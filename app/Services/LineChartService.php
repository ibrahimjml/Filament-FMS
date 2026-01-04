<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\Outcome;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LineChartService
{
  public function getDashboardData()
  {
    $currrentMonth = now()->startOfMonth();
    $endofMonth = now()->endOfMonth();

    return [
      'lineData' => $this->getLineData($currrentMonth),
      'profitData' => $this->getProfitData($currrentMonth, $endofMonth),
    ];
  }
  protected function getLineData($month)
  {
    $labels = [];
    for ($d = 1; $d <= $month->daysInMonth; $d++) {
      $labels[] = $month->copy()->day($d)->format('M j');
    }
    $incomeData = $this->getQueryData(Payment::class, $month);
    $outcomeData = $this->getQueryData(Outcome::class, $month);


    return [
      'labels'       => $labels,
      'income_data'  => $incomeData,
      'outcome_data' => $outcomeData,
    ];
  }

  /**
   * @param class-string<Model> $model
   */
  protected function getQueryData(string $model, $month)
  {
    $start = $month->copy()->startOfMonth();
    $end   = $month->copy()->endOfMonth();

    $sumColumn = $model === Payment::class
      ? 'payment_amount'
      : 'amount';

    $query = $model::query()
      ->whereBetween('created_at', [$start, $end])
      ->selectRaw(
        'DAY(created_at) as day, SUM(' . $sumColumn . ') as total'
      )
      ->groupBy('day')
      ->pluck('total', 'day')
      ->all();

    $dailyData = [];
    for ($day = 1; $day <= $month->daysInMonth; $day++) {
      $dailyData[] = (float) ($query[$day] ?? 0);
    }


    return $dailyData;
  }
  protected function getProfitData($currrentMonth, $endofMonth)
  {
    $dailyProfit = Payment::query()
      ->where('status', PaymentStatus::PAID)
      ->whereBetween('created_at', [$currrentMonth, $endofMonth])
      ->selectRaw('DATE(created_at) as date, SUM(payment_amount) as total')
      ->groupBy('date')
      ->pluck('total', 'date');

    $labels = [];
    $profits = [];

    for ($d = 1; $d <= $currrentMonth->daysInMonth; $d++) {
      $date = $currrentMonth->copy()->day($d)->toDateString();

      $labels[]  = $currrentMonth->copy()->day($d)->format('M j');
      $profits[] = $dailyProfit[$date] ?? 0;
    }
    array_unshift($profits, 0);
    array_unshift($labels, '');
    return [
      'labels' => $labels,
      'profit' => $profits,
    ];
  }
}
