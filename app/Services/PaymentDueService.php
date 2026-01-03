<?php

namespace App\Services;

use App\Models\Income;
use App\Enums\IncomeStatus;
use Carbon\Carbon;

class PaymentDueService
{
    protected function baseQuery()
    {
        return Income::query()
            ->with([
                'client',
                'unpaidPayments' => fn ($q) => $q->orderBy('next_payment'),
            ])
            ->withSum('paidPayments', 'payment_amount')
            ->where('status', '!=', IncomeStatus::COMPLETED->value)
            ->whereHas('client')
            ->whereHas('unpaidPayments');
    }

    // protected function attachNextPaymentAmount($collection)
    // {
    //     return $collection->map(function ($income) {
    //         $nextPayment = $income->unpaidPayments->first();
    //         $income->next_payment_amount = $nextPayment?->payment_amount ?? 0;
    //         return $income;
    //     });
    // }

    public function today()
    {
        $today = Carbon::today();

        return 
            $this->baseQuery()
                ->whereHas('unpaidPayments', fn ($q) =>
                    $q->whereDate('next_payment', '=', $today)
    );
              
        
    }

    public function overdue()
    {
        $today = Carbon::today();

        return 
            $this->baseQuery()
                ->whereHas('unpaidPayments', fn ($q) =>
                    $q->whereDate('next_payment', '<', $today)
    );
              
        
    }

    public function upcoming()
    {
        $today = Carbon::today();

        return 
            $this->baseQuery()
                ->whereHas('unpaidPayments', fn ($q) =>
                    $q->whereDate('next_payment', '>', $today)
                )
              
        ;
    }
}
