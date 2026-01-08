<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use App\Models\Invoice as InvoiceModel;
use Filament\Resources\Pages\Page;

class Invoice extends Page
{
    protected static string $resource = InvoiceResource::class;

    protected string $view = 'filament.resources.invoices.pages.invoice';
    public $record;
    public $invoice;
    public function mount($record)
    {
      $this->record = $record;
      $this->invoice = InvoiceModel::with(['client','payments','income.subcategory.category'])->findOrFail($record);
    }
}
