<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Incomes\IncomeResource;
use App\Models\Income;
use App\Services\PaymentDueService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class UpcomingPayment extends TableWidget
{
  protected static bool $isLazy = false;
  protected static ?int $sort = 4;
  public function getHeading(): ?string
  {
    return __('Upcomeing Payments');
  }
  public function table(Table $table): Table
  {
    $upcoming = app(PaymentDueService::class);
    $upcoming->upcoming();

    return $table
      ->query(fn() => $upcoming->upcoming())
      ->columns([
        Split::make([
          Stack::make([
            TextColumn::make('client.client_fname')
              ->icon('heroicon-m-user')
              ->iconColor('info')
              ->label('Client')
              ->weight(FontWeight::Bold)
              ->state(fn($record) => "{$record->client?->full_name}")
              ->extraAttributes(['class' => 'text-2xl capitalize pb-1'])
              ->searchable(),
            Stack::make([
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
              TextColumn::make('next_payment_amount')
                ->label(__('amount'))
                ->icon('heroicon-m-currency-dollar')
                ->iconColor('primary')
                ->getStateUsing(fn($record) => $record->next_payment_amount)
                ->money('USD')
                ->extraAttributes(['class' => 'flex justify-end items-center mt-4']),
            ])->extraAttributes(['class' => 'flex flex-row items-center']),
            TextColumn::make('next_payment')
              ->date()
              ->description(fn ($record) => next_payment_date($record->next_payment))
              ->icon('heroicon-m-clock')
              ->iconColor('primary')
              ->prefix('Due date: ')
              ->sortable()
              ->extraAttributes(['class' => 'text-green-400']),
          ])->grow()
        ])
      ])
      ->recordActions([
        Action::make('view')
          ->url(fn(Income $record): string => IncomeResource::getUrl('view', ['record' => $record->income_id]))
          ->label(__('View'))
          ->icon('heroicon-o-eye')
          ->color('info')
      ])
      ->toolbarActions([
        BulkActionGroup::make([
          //
        ]),
      ]);
  }
}
