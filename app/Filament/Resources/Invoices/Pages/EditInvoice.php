<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use App\Models\Invoice;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;
        protected function mutateFormDataBeforeSave(array $data): array
    {
        
            $income  = $data['income_id'] ? \App\Models\Income::find($data['income_id']) : null;
            $payment = $data['payment_id'] ? \App\Models\Payment::find($data['payment_id']) : null;

            $data['amount']         = $income?->amount ?? 0;
            $data['payment_amount'] = $payment?->payment_amount ?? 0;

        return $data;
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
