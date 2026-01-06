<?php

use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('invoices')->group(function(){
  Route::get('{invoice}',[InvoiceController::class,'index'])->name('invoice');
  Route::get('{invoice}/pdf',[InvoiceController::class,'download'])->name('invoice.pdf');
});
