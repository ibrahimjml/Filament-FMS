<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use App\Models\Payment;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditInvoice extends EditRecord
{
  protected static string $resource = InvoiceResource::class;
  protected function mutateFormDataBeforeFill(array $data): array
  {
    $invoice = $this->record;
    $data['payment_id'] = $invoice->payments()->pluck('payment_id')->toArray();
    return $data;
  }

  protected function mutateFormDataBeforeSave(array $data): array
  {

    $income  = $data['income_id'] ? \App\Models\Income::find($data['income_id']) : null;
    $data['amount'] = $income?->amount ?? 0;

    return $data;
  }
  protected function afterSave(): void
  {
    $invoice = $this->record;

    $paymentIds = (array) ($this->data['payment_id'] ?? []);

    Payment::where('invoice_id', $invoice->invoice_id)
      ->whereNotIn('payment_id', $paymentIds)
      ->update(['invoice_id' => null]);

    if (!empty($paymentIds)) {
      Payment::whereIn('payment_id', $paymentIds)
        ->update(['invoice_id' => $invoice->invoice_id]);
    }

    $invoice->updateQuietly([
      'payment_amount' => Payment::whereIn('payment_id', $paymentIds)
        ->sum('payment_amount'),
    ]);
  }

  protected function getHeaderActions(): array
  {
    return [
      ViewAction::make(),
      DeleteAction::make(),
      ForceDeleteAction::make(),
      RestoreAction::make(),
    ];
  }
}
