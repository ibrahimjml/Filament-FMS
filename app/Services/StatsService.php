<?php

namespace App\Services;

use App\Enums\IncomeStatus;
use App\Enums\PaymentStatus;
use App\Models\Client;
use App\Models\Income;
use App\Models\Outcome;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class StatsService
{
  public function getDashboardData($startDate = null, $endDate = null)
  {

    if ($startDate && $endDate) {
      $start = Carbon::parse($startDate)->startOfDay();
      $end   = Carbon::parse($endDate)->endOfDay();
    } else {
      $start = now()->startOfMonth();
      $end   = now()->endOfMonth();
    }

    return [
      'stats' => $this->getFinancialStat($start, $end),
    ];
  }
  protected function getFinancialStat($start, $end)
  {

    $totalIncome = $this->getTotalIncome($start, $end);
    $incomesChart = $this->getIncomesChart($start, $end);
    $totalOutcome = $this->getTotalOtcome($start, $end);
    $totalClients = $this->getTotalClients($start, $end);
    $totalUpcomingPayments = $this->getTotalUpcomingPayments($start, $end);



    return [
      'total_income'  => $totalIncome,
      'total_outcome' => $totalOutcome,
      'total_clients' => $totalClients,
      'incomes_chart' => $incomesChart,
      'profit'        => $totalIncome - $totalOutcome,
      'total_upcoming_payments' => $totalUpcomingPayments,

    ];
  }
  // total income
  protected function getTotalIncome($startDate, $endDate)
  {
    $query = Payment::with(['income'])->where('status', PaymentStatus::PAID->value);

    if ($startDate && $endDate) {
      $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    return $query->sum('payment_amount');
  }
  // total outcome
  protected function getTotalOtcome($startDate, $endDate)
  {
    $query = Outcome::query();

    if ($startDate && $endDate) {
      $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    return $query->sum('amount');
  }
  // total clients
  protected function getTotalClients($startDate, $endDate)
  {
    $query = Client::query();

    if ($startDate && $endDate) {
      $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    return $query->count();
  }
  protected function getIncomesChart($startDate, $endDate): array
  {
    $query = Payment::with(['income.client']);

    if ($startDate && $endDate) {
      $query->whereBetween('created_at', [$startDate, $endDate]);
    }
    return $query->selectRaw('DATE(created_at) as date, SUM(payment_amount) as amount')
      ->groupBy('date')
      ->orderBy('date')
      ->pluck('amount')
      ->toArray();
  }
  public function getTotalUpcomingPayments($startDate, $endDate)
  {
    $today = Carbon::today();

    return Income::query()
      ->with('unpaidPayments')
      ->withSum('paidPayments', 'payment_amount')
      ->where(function ($q) use ($today) {
        $q->whereDate('next_payment', '>', $today)
          ->orWhereDate('next_payment', $today);
      })
      ->when($startDate && $endDate, function (Builder $q) use ($startDate, $endDate) {
        $q->whereBetween('incomes.created_at', [$startDate, $endDate]);
      })
      ->where('status', '!=', IncomeStatus::COMPLETED->value)
      ->count();
  }
}
