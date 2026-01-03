<?php

namespace App\Filament\Widgets;

use App\Models\Income;
use App\Services\PaymentDueService;
use Filament\Actions\BulkActionGroup;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;


class OutdatedPayment extends TableWidget
{

  protected static bool $isLazy = false;
  protected static ?int $sort = 5;
  public function getHeading(): ?string
  {
    return __('Outdated Payments');
  }
  public function table(Table $table): Table
  {
    $outdated = app(PaymentDueService::class);
    $outdated->overdue();

    return $table
      ->query(fn() => $outdated->overdue())
      ->columns([
        TextColumn::make('client.client_fname')
          ->icon('heroicon-m-user')
          ->iconColor('info')
          ->label('Client')
          ->weight(FontWeight::Bold)
          ->state(fn($record) => "{$record->client?->full_name}")
          ->extraAttributes(['class' => 'text-2xl capitalize pb-1'])
          ->searchable(),
        TextColumn::make('subcategory.sub_name')
          ->icon('heroicon-m-tag')
          ->iconColor('warning')
          ->color('gray')
          ->state(
            fn($record) =>
            $record->subcategory?->name . ' - ' . $record->subcategory?->category?->name
          )
          ->extraAttributes(['class' => 'border-t dark:border-t-gray-200/20 pt-1'])
          ->sortable(),
        TextColumn::make('next_payment')
          ->date(fn($state) => $state?->format('m-d-Y'))
          ->icon('heroicon-m-calendar')
          ->iconColor('primary')
          ->prefix('Due date: ')
          ->sortable()
          ->extraAttributes(['class' => 'text-green-400']),
        TextColumn::make('next_payment_amount')
          ->prefix(__('Next Payment') . ' : ')
          ->icon('heroicon-m-currency-dollar')
          ->iconColor('primary')
          ->getStateUsing(fn($record) => $record->next_payment_amount)
          ->money('USD'),
      ])
      ->toolbarActions([
        BulkActionGroup::make([
          //
        ]),
      ]);
  }
}
