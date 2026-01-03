<?php

namespace App\Filament\Resources\Incomes\Pages;

use App\Enums\IncomeStatus;
use App\Enums\PaymentType;
use App\Filament\Resources\Incomes\IncomeResource;
use App\Models\Income;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListIncomes extends ListRecords
{
  protected static string $resource = IncomeResource::class;
  public function getTabs(): array
  {
    return [
      'all' => Tab::make()
           ->label(__('All')),
      'onetime' => Tab::make()
           ->label(PaymentType::ONETIME->getLabel())->badge(fn() => Income::query()->where('payment_type', PaymentType::ONETIME->value)->count())
           ->badgeColor('gray')
           ->icon(fn() => PaymentType::ONETIME->icon())
           ->modifyQueryUsing(fn(Builder $query) => $query->where('payment_type', PaymentType::ONETIME->value)),
      'recurring' => Tab::make()
           ->label(PaymentType::RECURRING->getLabel())
           ->badge(fn() => Income::query()->where('payment_type', PaymentType::RECURRING->value)->count())
           ->badgeColor('gray')
           ->icon(fn() => PaymentType::RECURRING->icon())
           ->modifyQueryUsing(fn(Builder $query) => $query->where('payment_type', PaymentType::RECURRING->value)),
      'completed' => Tab::make()
           ->label(IncomeStatus::COMPLETED->getLabel())
           ->badge(fn() => Income::query()->where('status', IncomeStatus::COMPLETED)->count())
           ->badgeColor(IncomeStatus::COMPLETED->getColor())
           ->icon(fn() => IncomeStatus::COMPLETED->icon())
           ->modifyQueryUsing(fn(Builder $query) => $query->where('status', IncomeStatus::COMPLETED)),
      'partial' => Tab::make()
           ->label(IncomeStatus::PARTIAL->getLabel())
           ->badge(fn() => Income::query()->where('status', IncomeStatus::PARTIAL)->count())
           ->badgeColor(IncomeStatus::PARTIAL->getColor())
           ->icon(fn() => IncomeStatus::PARTIAL->icon())
           ->modifyQueryUsing(fn(Builder $query) => $query->where('status', IncomeStatus::PARTIAL)),
      'pending' => Tab::make()
           ->label(IncomeStatus::PENDING->getLabel())
           ->badge(fn() => Income::query()->where('status', IncomeStatus::PENDING)->count())
           ->badgeColor(IncomeStatus::PENDING->getColor())
           ->icon(fn() => IncomeStatus::PENDING->icon())
           ->modifyQueryUsing(fn(Builder $query) => $query->where('status', IncomeStatus::PENDING)),
    ];
  }
  protected function getHeaderActions(): array
  {
    return [
      CreateAction::make(),
    ];
  }
}
