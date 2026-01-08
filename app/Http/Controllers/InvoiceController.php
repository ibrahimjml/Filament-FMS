<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{

    public function index(Invoice $invoice)
    {
         $invoice = $this->getInvoiceDetail($invoice);
         return view('invoices.invoice-pdf',['invoice' => $invoice]);
    }
    public function download(Invoice $invoice)
    {
      $invoice = $this->getInvoiceDetail($invoice);
      $pdf = Pdf::loadView('invoices.invoice-pdf',['invoice' => $invoice])->setPaper('a4','portrait');
      return $pdf->download('invoice-'.$invoice->invoice_number.'.pdf');
    }
    public function getInvoiceDetail($invoice)
    {
      return Invoice::with(['client','payments','income.subcategory.category'])->findOrFail($invoice->invoice_id);
    }
}
