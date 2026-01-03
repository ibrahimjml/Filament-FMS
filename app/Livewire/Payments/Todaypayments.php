<?php

namespace App\Livewire\Payments;

use App\Filament\Resources\Incomes\IncomeResource;
use App\Models\Income;
use App\Services\PaymentDueService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class Todaypayments extends Component implements HasSchemas, HasTable, HasForms
{
  use InteractsWithSchemas, InteractsWithTable, InteractsWithActions;
  public $count;
  public function mount(PaymentDueService $service): void
  {
    $this->count = $service->today()->count();
  }
  public function table(Table $table): Table
  {
$table = IncomeResource::table($table);

    return $table
      ->columns(array_merge($table->getColumns(), [
        Stack::make([
          TextColumn::make('next_payment_amount')
            ->prefix(__('Today Payment').' : ')
            ->icon('heroicon-m-currency-dollar')
            ->iconColor('primary')
            ->getStateUsing(fn($record) => $record->next_payment_amount)
            ->money('USD'),
        ])
      ]))->extraAttributes(['class' => 'flex gap-3 '])
      ->query(
        Income::query()
          ->with(['client', 'unpaidPayments' => fn($q) => $q->orderBy('next_payment')])
          ->withSum('paidPayments', 'payment_amount')
          ->where('status', '!=', \App\Enums\IncomeStatus::COMPLETED->value)
          ->whereHas('client')
          ->whereHas('unpaidPayments', fn($q) => $q->whereDate('next_payment', '=', now()))
      )
      ->recordActions([
        Action::make('view')
          ->url(fn(Income $record): string => IncomeResource::getUrl('view', ['record' => $record->income_id]))
          ->label(__('View'))
          ->icon('heroicon-o-eye')
          ->color('gray')
      ]);  }
  public function render()
  {
    return view('livewire.payments.todaypayments');
  }
}
