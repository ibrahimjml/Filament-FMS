<?php

use App\Models\Discount;
use Carbon\Carbon;

if(!function_exists('recalcFinalAmount')){
function recalcFinalAmount(callable $set, callable $get): void
{
    $amount = (float) $get('amount');

    if (! $amount || ! $get('discount_id')) {
       $set('final_amount', 0);
        return;
    }

    $discount = Discount::find($get('discount_id'));

    $set(
        'final_amount',
        $discount?->apply($amount) ?? 0
    );
}

if (! function_exists('next_payment_date')) {
    function next_payment_date(?Carbon $nextPayment): string
    {
        $days = today()->diffInDays(
            $nextPayment->startOfDay(),
            false
        );

        if ($days < 0) {
            return abs($days) . __('days overdue');
        }

        if ($days === 0) {
            return __('Due today');
        }

        return $days . __(' days remaining');
    }
}
}