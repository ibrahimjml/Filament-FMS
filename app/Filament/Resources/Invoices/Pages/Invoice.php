<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Pages\InvoiceSetting;
use App\Filament\Resources\Invoices\InvoiceResource;
use App\Models\Invoice as InvoiceModel;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\Page;

class Invoice extends Page implements HasActions
{
  use InteractsWithActions;
  protected static string $resource = InvoiceResource::class;

  protected string $view = 'filament.resources.invoices.pages.invoice';
  public $record;
  public $invoice;
  public function mount($record)
  {
    $this->record = $record;
    $this->invoice = InvoiceModel::with(['client', 'payments', 'income.subcategory.category'])->findOrFail($record);
  }
  protected function getHeaderActions(): array
  {
    return [
      EditAction::make()
        ->label(__('Edit'))
        ->icon('heroicon-o-pencil')
        ->color('info'),
      Action::make('setting')
        ->label(__('Setting'))
        ->icon('heroicon-o-cog-8-tooth')
        ->color('gray')
        ->url(fn() => InvoiceSetting::getUrl()),
      Action::make('print')
        ->label(__('Print'))
        ->icon('heroicon-o-printer')
        ->color('warning')
        ->action(fn() => $this->dispatch('print-invoice')),
    ];
  }
}
