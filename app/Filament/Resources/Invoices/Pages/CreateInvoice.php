<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Enums\InvoiceStatus;
use App\Filament\Resources\Invoices\InvoiceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoice extends CreateRecord
{
  protected static string $resource = InvoiceResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
  {
    
        $income  = $data['income_id'] ? \App\Models\Income::find($data['income_id']) : null;
        $payment = $data['payment_id'] ? \App\Models\Payment::find($data['payment_id']) : null;
      return [
        'invoice_number' => $data['invoice_number'] ?? null,
        'client_id'      => $data['client_id'] ?? null,
        'income_id'      => $data['income_id'] ?? null,
        'payment_id'     => $data['payment_id'] ?? null,
         'amount'        => $income?->amount ?? 0,
        'payment_amount' => $payment?->payment_amount ?? 0,
        'issue_date'     => now()->toDateString(),
        'status'         => InvoiceStatus::from($data['status']),
        'description'    => $data['description'] ?? null,
      
      ];
    
  }
}
