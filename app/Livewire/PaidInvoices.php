<?php

namespace App\Livewire;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
use App\Models\Invoice;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class PaidInvoices extends StatsOverviewWidget
{
  protected static bool $isLazy = false;
  protected function getStats(): array
  {
    return [
      Stat::make(__('Total Paid Invoices'), '$ ' . number_format($this->getTotalPaid()))
        ->description(__('Total Paid Invoices'))
        ->icon('heroicon-m-receipt-percent')
        ->color('primary'),
    ];
  }
  protected function getTotalPaid()
  {
      return Invoice::query()
      ->where('status', InvoiceStatus::PAID->value)
      ->whereHas('payments', function (Builder $q) {
        $q->where('status', PaymentStatus::PAID->value);
      })
      ->withSum([
        'payments as total_paid' => function (Builder $q) {
          $q->where('status', PaymentStatus::PAID->value);
        }
      ], 'payment_amount')
      ->get()
      ->sum('total_paid');
  }

}
