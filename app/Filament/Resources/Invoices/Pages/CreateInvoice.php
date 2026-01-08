<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Enums\InvoiceStatus;
use App\Filament\Resources\Invoices\InvoiceResource;
use App\Models\Payment;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoice extends CreateRecord
{
  protected static string $resource = InvoiceResource::class;
  protected function mutateFormDataBeforeCreate(array $data): array
  {

    $income  = $data['income_id'] ? \App\Models\Income::find($data['income_id']) : null;
    $invoiceSetting = \App\Models\InvoiceSetting::first();
    return [
      'invoice_number'     => $data['invoice_number'] ?? null,
      'invoice_setting_id' => $invoiceSetting?->invoice_setting_id ?? null,
      'client_id'          => $data['client_id'] ?? null,
      'income_id'          => $data['income_id'] ?? null,
      'amount'             => $income?->amount ?? 0,
      'payment_amount'     => isset($data['payment_id']) 
                                ? Payment::whereIn('payment_id', (array) $data['payment_id'])->sum('payment_amount') 
                                : 0,
      'issue_date'         => now()->toDateString(),
      'status'             => InvoiceStatus::from($data['status']),
      'description'        => $data['description'] ?? null,
      'setting_snapshot'   => [
              'company_name'    => $invoiceSetting?->company_name ?? null,
              'company_email'   => $invoiceSetting?->company_email ?? null,
              'company_phone'   => $invoiceSetting?->company_phone ?? null,
              'company_address' => $invoiceSetting?->company_address ?? null,
              'logo'            => $invoiceSetting?->logo ?? null,
              'footer'          => $invoiceSetting?->footer ?? null,
      ]

    ];
  }
protected function afterCreate(): void
{
    $invoice = $this->record;
    $paymentIds = $this->data['payment_id'] ?? [];

    if (!empty($paymentIds)) {
        Payment::whereIn('payment_id', (array) $paymentIds)
            ->update(['invoice_id' => $invoice->invoice_id]);
    }
}


}
