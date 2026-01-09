<?php

namespace App\Filament\Resources\Incomes\Pages;

use App\Enums\IncomeStatus;
use App\Enums\PaymentStatus;
use App\Filament\Resources\Incomes\IncomeResource;
use App\Models\Income;
use App\Models\Payment;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\App;

class CreateIncome extends CreateRecord
{
    protected static string $resource = IncomeResource::class;
    protected  $income;

    protected $paid;
    protected $payment_status;
    protected $next_payment;
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

     
     $this->paid = (float) ($data['payments']['payment_amount'] ?? $data['payments.payment_amount'] ?? 0);
     $this->payment_status = $data['payments']['status'] ?? $data['payments.status'] ?? null;
     $this->next_payment = $data['next_payment'] ?? null;

    if (App::getLocale() !== 'en') {

      $this->translatedData = [
        'description' => $data['description'],
      ];
      $data['description'] ??= $this->translatedData['description'];
    }

    
    unset($data['payments']);

    return $data;
  }
  protected function afterCreate(): void
  {
     $income = $this->record;
    $executeAmount =  $income->final_amount > 0 ? $income->final_amount : $income->amount;

    if (!empty($this->paid) && $this->paid > 0) {
        if ($this->paid > $executeAmount) {
            throw new \Exception('Paid amount cannot be greater than the total due amount.');
        }

      $payment =  Payment::create([
            'income_id'      => $income->income_id,
            'payment_amount' => $this->paid,
            'status'         => PaymentStatus::from($this->payment_status),
            'next_payment'   => $this->next_payment, 
        ]);
        
      if($payment->status->value === PaymentStatus::PAID->value){
        $payment->update([
          'paid_at' => now()
        ]);
      }  
    }

    $totalPaid = Payment::where('income_id', $income->income_id)
                        ->where('status', PaymentStatus::PAID->value)
                        ->sum('payment_amount');

    $income->status = match (true) {
        $executeAmount > 0 && $totalPaid >= $executeAmount => IncomeStatus::COMPLETED->value,
        $executeAmount > 0 && $totalPaid > 0 => IncomeStatus::PARTIAL->value,
        default => IncomeStatus::PENDING->value,
    };
    $income->save();

    if ( App::getLocale() !== 'en' && !empty($this->translatedData)) {
      $this->record->translations()->create([
        'lang_code'    => App::getLocale(),
        'description' => $this->translatedData['description'],
      ]);
    }
  }

  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }

}