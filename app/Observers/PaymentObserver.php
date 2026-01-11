<?php

namespace App\Observers;

use App\Enums\IncomeStatus;
use App\Enums\PaymentStatus;
use App\Models\Event;
use App\Models\Income;
use App\Models\Payment;
use App\Services\IncomeService;
use Illuminate\Support\Facades\DB;

class PaymentObserver
{
  public function creating(Payment $payment): void
  {
    $income = \App\Models\Income::find($payment->income_id);
    if ($income && ($income->remaining <= 0 || $income->status === IncomeStatus::COMPLETED->value)) {
      throw new \Exception('Cannot add payment to a completed income');
    }
  }
  public function created(Payment $payment): void
  {
    // recalculate income status and next payment
    IncomeService::recalculateIncomeStatusFor($payment->income_id);

    IncomeService::updateIncomeNextPayment($payment->income_id);

    if ($payment->status->value === PaymentStatus::UNPAID->value) {
      Event::create([
        'payment_id' => $payment->payment_id,
        'event_name' => $payment->income->client->full_name . ' - $' . $payment->payment_amount,
        'start_date' => $payment->next_payment,
        'end_date'   => $payment->next_payment,
        'color'      => '#b80f0f',
        'bg_color'   => '#b80f0f'
      ]);
    }
  }

  public function updated(Payment $payment): void
  {

    DB::transaction(function () use ($payment) {

      Payment::withoutEvents(function () use ($payment) {
        if ($payment->status->value === PaymentStatus::PAID->value) {
          $payment->update([
            'next_payment' => null,
          ]);
        } else {
          $payment->update([
            'paid_at' => null,
          ]);
        }
      });

      $payment->load(['income.client']);

      $incomeId = $payment->income_id;

      // recalculate income status and next payment
      if ($incomeId) {
        IncomeService::recalculateIncomeStatusFor($incomeId);
        IncomeService::updateIncomeNextPayment($incomeId);
      }

      // create event on unpaid
      if ($payment->status->value === PaymentStatus::UNPAID->value) {
        Event::create([
          'payment_id' => $payment->payment_id,
          'event_name' => $payment->income->client->full_name . ' - $' . $payment->payment_amount,
          'start_date' => $payment->next_payment,
          'end_date'   => $payment->next_payment,
          'color'      => '#b80f0f',
          'bg_color'   => '#b80f0f',
        ]);
      }
    });
  }

  public function deleted(Payment $payment): void
  {
    IncomeService::recalculateIncomeStatusFor($payment->income_id);  // recalculate income status and next payment

    $payment->income->update([
      'next_payment' => null
    ]);

    $event = Event::query()->where('payment_id', $payment->payment_id)->exists();
    if ($event) {
      Event::query()->where('payment_id', $payment->payment_id)->delete();
    }
  }


  public function restored(Payment $payment): void
  {
    IncomeService::recalculateIncomeStatusFor($payment->income_id);  // recalculate income status and next payment
  }
}
