<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Enums\PaymentStatus;
use App\Filament\Resources\Payments\PaymentResource;
use App\Models\Payment;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;
    public function getTabs(): array
  {
    return [
      'all' => Tab::make()
           ->label(__('All')),
      'paid' => Tab::make()
           ->label(PaymentStatus::PAID->getLabel())->badge(fn() => Payment::query()->where('status', PaymentStatus::PAID->value)->count())
           ->badgeColor('gray')
           ->modifyQueryUsing(fn(Builder $query) => $query->where('status', PaymentStatus::PAID->value)),
      'unpaid' => Tab::make()
           ->label(PaymentStatus::UNPAID->getLabel())
           ->badge(fn() => Payment::query()->where('status', PaymentStatus::UNPAID->value)->count())
           ->badgeColor('gray')
           ->modifyQueryUsing(fn(Builder $query) => $query->where('status', PaymentStatus::UNPAID->value)),
      'canceled' => Tab::make()
           ->label(PaymentStatus::CANCELED->getLabel())
           ->badge(fn() => Payment::query()->where('status', PaymentStatus::CANCELED->value)->count())
           ->badgeColor('gray')
           ->modifyQueryUsing(fn(Builder $query) => $query->where('status', PaymentStatus::CANCELED->value)),
    ];
  }
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->hidden(),
        ];
    }
}
