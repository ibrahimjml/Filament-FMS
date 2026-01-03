<?php

namespace App\Services;

use App\Enums\IncomeStatus;
use App\Enums\PaymentStatus;
use App\Models\Income;
use App\Models\Payment;

class IncomeStatusService
{
      public static function recalculateIncomeStatusFor(int|null $incomeId): void
    {
        /** @var Income|null $income */
        $income = Income::find($incomeId);

        if (! $income) {
            return;
        }

        $executeAmount = $income->final_amount > 0 
                   ? $income->final_amount
                   : $income->amount;

        $totalPaid = Payment::where('income_id', $incomeId)
            ->where('status', PaymentStatus::PAID->value)
            ->sum('payment_amount');

        $income->status = match (true) {
             $totalPaid >= $executeAmount => IncomeStatus::COMPLETED->value,
             $totalPaid > 0 => IncomeStatus::PARTIAL->value,
            default => IncomeStatus::PENDING->value,
        };

        $income->save();
    }
}
