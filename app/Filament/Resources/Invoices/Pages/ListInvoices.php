<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Enums\InvoiceStatus;
use App\Filament\Pages\InvoiceSetting;
use App\Filament\Resources\Invoices\InvoiceResource;
use App\Livewire\PaidInvoices;
use App\Models\Invoice;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListInvoices extends ListRecords
{
  protected static string $resource = InvoiceResource::class;

  public function getTabs(): array
  {
    return [
      'all' => Tab::make()
        ->label(__('All')),
      'paid' => Tab::make()
        ->label(InvoiceStatus::PAID->getLabel())->badge(fn() => Invoice::query()->where('status', InvoiceStatus::PAID->value)->count())
        ->badgeColor('gray')
        ->modifyQueryUsing(fn(Builder $query) => $query->where('status', InvoiceStatus::PAID->value)),
      'partial' => Tab::make()
        ->label(InvoiceStatus::PARTIAL->getLabel())->badge(fn() => Invoice::query()->where('status', InvoiceStatus::PARTIAL->value)->count())
        ->badgeColor('gray')
        ->modifyQueryUsing(fn(Builder $query) => $query->where('status', InvoiceStatus::PARTIAL->value)),
      'overdue' => Tab::make()
        ->label(InvoiceStatus::OVERDUE->getLabel())->badge(fn() => Invoice::query()->where('status', InvoiceStatus::OVERDUE->value)->count())
        ->badgeColor('gray')
        ->modifyQueryUsing(fn(Builder $query) => $query->where('status', InvoiceStatus::OVERDUE->value)),

    ];
  }
  protected function getHeaderActions(): array
  {
    return [
      CreateAction::make()
        ->label(__('Create'))
        ->icon('heroicon-o-plus')
        ->color('success'),
      Action::make('setting')
        ->label(__('Setting'))
        ->icon('heroicon-o-cog-8-tooth')
        ->color('info')
        ->url(fn() => InvoiceSetting::getUrl())
    ];
  }
    protected function getHeaderWidgets(): array
    {
      return [
        PaidInvoices::class
      ];
    }
}
