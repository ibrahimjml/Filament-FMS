<?php

namespace App\Livewire\Payments;

use App\Enums\PaymentStatus;
use App\Filament\Resources\Incomes\IncomeResource;
use App\Models\Income;
use App\Services\PaymentDueService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Livewire\Component;

class ListPayments extends Component implements HasActions, HasSchemas, HasTable
{
  use InteractsWithActions;
  use InteractsWithTable;
  use InteractsWithSchemas;

  public string $type = 'upcoming';

  protected $listeners = ['paymentPriority' => '$refresh'];

  public function switchPaymentsTab(string $type): void
  {
    $this->type = $type;
    $this->resetTable();
  }

  public function table(Table $table): Table
  {
    $table = IncomeResource::table($table);

    return $table
      ->query(fn() => $this->getQuery())
      ->columns(array_merge($table->getColumns(), [
        Stack::make([
          TextColumn::make('next_payment_amount')
            ->prefix(__('will pay') . ' : ')
            ->icon('heroicon-m-currency-dollar')
            ->iconColor('primary')
            ->getStateUsing(fn($record) => $record->next_payment_amount)
            ->money('USD'),
          TextColumn::make('next_payment')
            ->getStateUsing(fn($record) => next_payment_date($record->next_payment))
            ->icon('heroicon-m-clock')
            ->iconColor('gray')
            ->sortable()
            ->extraAttributes(fn() => [
              'class' => match ($this->type) {
                'outdated' => 'text-red-500',
                'today'    => 'text-green-500',
                'upcoming' => 'text-yellow-400',
                default    => 'text-gray-500',
              }
            ]),
        ])
      ]))->extraAttributes(['class' => 'flex gap-3 '])
      ->recordActions([
        Action::make('is_priority')
          ->hiddenLabel()
          ->icon(fn($record) => $record->unpaidPayments->first()?->is_priority ? 'heroicon-s-shield-exclamation' : 'heroicon-o-shield-exclamation')
          ->color(fn($record) => $record->unpaidPayments->first()?->is_priority ? 'warning' : 'gray')
          ->size('xl')
          ->action(function ($record, Component $livewire) {
            $payment = $record->unpaidPayments()->orderBy('next_payment')->first();
            if ($payment) {
              $payment->is_priority = !$payment->is_priority;
              $payment->save();
            }
            $livewire->dispatch('paymentPriority');
          })
          ->successNotification(function ($record) {
            $payment = $record->unpaidPayments()->orderBy('next_payment')->first();
            if ($payment && $payment->is_priority) {
              return Notification::make()
                ->success()
                ->title(__('Priority'))
                ->body(__('Added to priority list'));
            }
          }),
        Action::make('next_payment')
          ->label(
            fn($record): string => $record->payments?->where('status', PaymentStatus::PAID)->count() ?? 0
          )->icon('heroicon-m-currency-dollar')
          ->color('gray')
          ->url(fn($record) => IncomeResource::getUrl('payments', ['record' => $record->income_id])),
        ActionGroup::make([
          Action::make('view')
            ->url(fn(Income $record): string => IncomeResource::getUrl('view', ['record' => $record->income_id]))
            ->hiddenLabel()
            ->icon('heroicon-o-eye')
            ->color('primary')
        ])
      ])
      ->emptyStateDescription(fn() => $this->getEmptyState());
  }

  protected function getQuery(): Builder
  {
    return match ($this->type) {
      'outdated' => app(PaymentDueService::class)->overdue(),
      'today'    => app(PaymentDueService::class)->today(),
      'priority' =>app(PaymentDueService::class)->byPriority(),
      default    => app(PaymentDueService::class)->upcoming(),
    };
  }
  protected function getEmptyState(): string
  {
    return match ($this->type) {
      'outdated' => __('No outdated payments found.'),
      'today'    => __('No payments today.'),
      default    => __('No upcoming payments found.'),
    };
  }

  public function render(): View
  {
    return view('livewire.payments.list-payments');
  }
}
