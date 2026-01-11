<?php

namespace App\Filament\Resources\Incomes\Pages;

use App\Enums\IncomeStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Filament\Resources\Incomes\IncomeResource;
use App\Models\Income;
use App\Models\Payment;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\App;
use App\Services\IncomeService;

class CreateIncome extends CreateRecord
{
    protected static string $resource = IncomeResource::class;
    protected  $income, $paid, $payment_status, $paymentType, $next_payment;
    protected array $paymentsData = [];
    protected array $translatedData = [];
    protected function mutateFormDataBeforeCreate(array $data): array
  {
    $amount = (float) ($data['amount'] ?? 0);

    if (!empty($data['discount_id'])) {
        $discount = \App\Models\Discount::find($data['discount_id']);
        $data['final_amount'] = $discount?->apply($amount) ?? 0;
    } else {
        $data['final_amount'] = 0;
    }

     $data['status'] = IncomeStatus::PENDING->value;

     
    $this->paymentType = $data['payment_type'] ?? PaymentType::ONETIME->value;

    if ($this->paymentType === PaymentType::ONETIME->value) {
    $this->paymentsData = [
        'payment_amount' => $data['payment_amount'] ?? 0,
        'status'         => $data['payment_status'] ?? null,
        'next_payment'   => $data['next_payment'] ?? null,
      ];
  } else {
    $this->paymentsData = $data['payments'] ?? [];
  }

    if (App::getLocale() !== 'en') {

      $this->translatedData = [
        'description' => $data['description'],
      ];
      $data['description'] ??= $this->translatedData['description'];
    }

    
      unset(
    $data['payment_amount'],
    $data['payment_status'],
    $data['next_payment'],
    $data['payments'],
);

    return $data;
  }
protected function afterCreate(): void
{
    $income = $this->record;
    $executeAmount = $income->final_amount > 0
        ? $income->final_amount
        : $income->amount;

    if ($this->paymentType === PaymentType::ONETIME->value) {
        $this->createOneTimePayment($income, $executeAmount);
    }

    if ($this->paymentType === PaymentType::RECURRING->value) {
        $this->createRecurringPayments($income);
    }

    IncomeService::updateIncomeNextPayment($income->income_id);  // update income status

    if (App::getLocale() !== 'en' && !empty($this->translatedData)) {
        $income->translations()->create([
            'lang_code'    => App::getLocale(),
            'description' => $this->translatedData['description'],
        ]);
    }
}
protected function createOneTimePayment(Income $income, float $executeAmount): void
{
    $paid   = (float) ($this->paymentsData['payment_amount'] ?? 0);
    $status = $this->paymentsData['status'] ?? null;

    if ($paid <= 0 || !$status) {
        return;
    }

    if ($paid > $executeAmount) {
        throw new \Exception('Paid amount cannot exceed total amount.');
    }

    Payment::create([
        'income_id'      => $income->income_id,
        'payment_amount' => $paid,
        'status'         => PaymentStatus::from($status),
        'next_payment'   => $this->paymentsData['next_payment'],
        'paid_at'        => $status === PaymentStatus::PAID->value ? now() : null,
    ]);
}

protected function createRecurringPayments(Income $income): void
{
    foreach ($this->paymentsData as $paymentData) {
        Payment::create([
            'income_id'      => $income->income_id,
            'payment_amount' => $paymentData['payment_amount'],
            'status'         => PaymentStatus::from($paymentData['status']),
            'next_payment'   => $paymentData['next_payment'] ?? null,
            'paid_at'        => $paymentData['status'] === PaymentStatus::PAID->value
                                ? now()
                                : null,
        ]);
    }
}

  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }

}