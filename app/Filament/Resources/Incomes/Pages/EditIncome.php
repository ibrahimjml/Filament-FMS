<?php

namespace App\Filament\Resources\Incomes\Pages;


use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Filament\Resources\Incomes\IncomeResource;
use App\Models\Payment;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\App;
use App\Services\IncomeService;

class EditIncome extends EditRecord
{
  protected static string $resource = IncomeResource::class;
  protected float $paid = 0;
  protected ?string $payment_status = null;
  protected ?string $next_payment = null;
  protected array $paymentsData = [];
  protected array $translatedData = [];
  protected function mutateFormDataBeforeFill(array $data): array
  {
    $data['description'] = strip_tags($this->record->trans_description);

    if ($this->record->payment_type === PaymentType::ONETIME) {
          
      $data['payment_amount'] = $this->record->payments->first()?->payment_amount ?? 0;
      $data['payment_status'] = $this->record->payments->first()?->status?->value ?? null;
      $data['next_payment'] = $this->record->payments->first()?->next_payment ?? null;
    } else {
      $data['payments'] = $this->record->payments
        ->map(fn($p) => [
          'payment_amount' => $p->payment_amount,
          'status' => $p->status->value,
          'next_payment' => $p->next_payment,
        ])
        ->toArray();
    }

    return $data;
  }
protected function mutateFormDataBeforeSave(array $data): array
{
    $amount = (float) ($data['amount'] ?? 0);

    if (!empty($data['discount_id'])) {
        $discount = \App\Models\Discount::find($data['discount_id']);
        $data['final_amount'] = $discount?->apply($amount) ?? 0;
    }

    if ($data['payment_type'] === PaymentType::ONETIME->value) {
        $this->paymentsData = [
            [
                'payment_amount' => $data['payment_amount'] ?? 0,
                'status'         => $data['payment_status'] ?? null,
                'next_payment'   => $data['next_payment'] ?? null,
                'id'             => $this->record->payments->first()?->payment_id ?? null,
            ]
        ];
    } else {
        $this->paymentsData = collect($data['payments'])->map(function ($payment, $index) {
            return [
                'id'             => $this->record->payments[$index]->payment_id ?? null,
                'payment_amount' => $payment['payment_amount'],
                'status'         => $payment['status'],
                'next_payment'   => $payment['next_payment'] ?? null,
            ];
        })->toArray();
    }

    if (App::getLocale() !== 'en') {
        $this->translatedData = [
            'description' => $data['description'],
        ];
        unset($data['description']);
    }

    unset($data['payment_amount'],
     $data['payment_status'], 
     $data['next_payment'],
      $data['payments']);

    return $data;
}
protected function afterSave(): void
{
    $income = $this->record;
    $existingPaymentIds = $income->payments()->pluck('payment_id')->toArray();

    $submittedPaymentIds = [];
    foreach ($this->paymentsData as $paymentData) {
        if (!empty($paymentData['id'])) {
          $submittedPaymentIds[] = $paymentData['id'];
            Payment::updateOrCreate(
    ['payment_id' => $paymentData['id'] ?? null],
        [
                 'income_id'      => $income->income_id,
                 'payment_amount' => $paymentData['payment_amount'],
                 'status'         => PaymentStatus::from($paymentData['status']),
                 'next_payment'   => $paymentData['next_payment'],
                 'paid_at'        => $paymentData['status'] === PaymentStatus::PAID->value ? now() : null,
               ]);

        } 
    }

     $paymentsToDelete = array_diff($existingPaymentIds, $submittedPaymentIds);
    Payment::whereIn('payment_id', $paymentsToDelete)->delete();

  IncomeService::recalculateIncomeStatusFor($income->income_id); // Update Income status
    

    // Update translations
    if (App::getLocale() !== 'en' && !empty($this->translatedData)) {
        $income->translations()->updateOrCreate(
            ['lang_code' => App::getLocale()],
            ['description' => $this->translatedData['description']]
        );
    }
}
  protected function getHeaderActions(): array
  {
    return [

      DeleteAction::make(),
      ForceDeleteAction::make(),
      RestoreAction::make(),
    ];
  }
}
