<?php

namespace App\Filament\Resources\Incomes\Tables;

use App\Enums\PaymentType;
use App\Filament\Resources\Incomes\IncomeResource;
use App\Models\Income;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Grouping\Group;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;


class IncomesTable
{
  public static function configure(Table $table): Table
  {
    return $table
      ->reorderableColumns()
      ->columnManagerColumns(2)
      ->columns([
        Stack::make([

          Stack::make([
            TextColumn::make('client.client_fname')
              ->icon('heroicon-m-user')
              ->iconColor('info')
              ->label('Client')
              ->weight(FontWeight::Bold)
              ->formatStateUsing(fn($record) => "{$record->client?->full_name}")
              ->extraAttributes(['class' => 'text-xl capitalize pb-1 w-full'])
              ->searchable(),
            TextColumn::make('subcategory.sub_name')
              ->icon('heroicon-m-tag')
              ->iconColor('warning')
              ->color('gray')
              ->formatStateUsing(
                fn($record) =>
                $record->subcategory?->name . ' - ' . $record->subcategory?->category?->name
              )
              ->extraAttributes(['class' => 'border-t dark:border-t-gray-200/20 pt-1'])
              ->sortable(),
          ])->space(1),

          Stack::make([
            TextColumn::make('amount')
              ->weight(FontWeight::SemiBold)
              ->formatStateUsing(fn($record) => __('Amount') . ' : ' . number_format($record->amount, 2) . ' $')
              ->icon('heroicon-m-currency-dollar')
              ->iconColor('primary')
              ->sortable(),
            TextColumn::make('final_amount')
              ->formatStateUsing(fn($record) => __('Final Amount') . ' : ' . number_format($record->final_amount, 2) . ' $')
              ->weight(FontWeight::SemiBold)
              ->icon('heroicon-m-currency-dollar')
              ->iconColor('primary')
              ->sortable(),
          ]),

          Stack::make([
            TextColumn::make('total_paid')
              ->icon('heroicon-m-currency-dollar')
              ->iconColor('primary')
              ->weight(FontWeight::SemiBold)
              ->formatStateUsing(fn($record) => __('Paid') . ' : ' . number_format($record->total_paid, 2) . ' $'),
            TextColumn::make('remaining')
              ->weight(FontWeight::SemiBold)
              ->icon('heroicon-m-currency-dollar')
              ->iconColor('primary')
              ->formatStateUsing(fn($record) => __('Remaining') . ' : ' . number_format($record->remaining, 2) . ' $')

          ]),

          Stack::make([
            TextColumn::make('status')
              ->icon(fn($state) => $state?->icon())
              ->formatStateUsing(fn($state) => $state?->getLabel())
              ->badge()
              ->sortable()
              ->searchable(),
            TextColumn::make('payment_type')
              ->icon(fn($state) => $state?->icon())
              ->formatStateUsing(fn($state) => $state?->getLabel())
              ->badge()
              ->searchable(),

          ])->extraAttributes(['class' => 'flex flex-row gap-2 border-b dark:border-b-gray-200/20 pb-2']),

          Stack::make([
            TextColumn::make('next_payment')
              ->date()
              ->icon('heroicon-m-calendar')
              ->iconColor('primary')
              ->formatStateUsing(fn($record) => __('Next Payment') . ' : ' . $record->next_payment->format('m-d-Y'))
              ->sortable()
              ->extraAttributes(['class' => 'text-green-400']),
            TextColumn::make('created_at')
              ->date()
              ->icon('heroicon-m-plus-circle')
              ->iconColor('warning')
              ->formatStateUsing(fn($record) => __('Created At') . ' : ' . $record->created_at->format('m-d-Y'))
              ->sortable(),
            TextColumn::make('updated_at')
              ->date()
              ->icon('heroicon-m-pencil-square')
              ->iconColor('info')
              ->formatStateUsing(fn($record) => __('Updated At') . ' : ' . $record->updated_at->format('m-d-Y'))
              ->sortable(),
            TextColumn::make('deleted_at')
              ->date(fn($state) => $state?->format('m/d/Y'))
              ->sortable()
              ->toggleable(isToggledHiddenByDefault: true),
          ]),
        ])
          ->extraAttributes(['class' => 'flex gap-3'])
      ])
      ->groups([
        Group::make('client.full_name')
          ->label(__('Client Full Name')),
        Group::make('subcategory.sub_name')
          ->label(__('Subcategories')),
        Group::make('amount')
          ->label(__('Amount')),
        Group::make('final_amount')
          ->label(__('Final Amount')),
        Group::make('next_payment')
          ->label(__('Next Payment')),
        Group::make('created_at')
          ->label(__('Created At'))
          ->date(),
      ])
      ->defaultSort('income_id', 'desc')
      ->filters([
        TrashedFilter::make(),
      ])
      ->contentGrid([
        'sm'  => 1,
        'md'  => 2,
        'xl'  => 3,
        '2xl' => 4,
      ])

      ->recordActions([
        Action::make('is_priority')
          ->hiddenLabel()
          ->icon(fn($record) => $record->is_priority ? 'heroicon-s-shield-exclamation' : 'heroicon-o-shield-exclamation')
          ->color(fn($record) => $record->is_priority ? 'warning' : 'gray')
          ->size('xl')
          ->action(function ($record) {
            $record->is_priority = !$record->is_priority;
            $record->save();
          })
          ->successNotification(function ($record) {
            if ($record->is_priority) {
              return Notification::make()
                ->success()
                ->title(__('Priority'))
                ->body(__('Added to priority list'));
            }
          }),
        Action::make('milestone')
          ->label(fn(Income $record) => $record->paidPayments->count() . ' / ' . $record->recurring_count)
          ->icon('heroicon-m-flag')
          ->color('gray')
          ->visible(fn(Income $record) => $record->payment_type === PaymentType::RECURRING)
          ->url(fn(Income $record): string => IncomeResource::getUrl('payments', ['record' => $record->income_id])),
        ActionGroup::make([
          ViewAction::make(),
          EditAction::make(),
          DeleteAction::make(),
        ])->color('gray')
      ])
      ->toolbarActions([

        BulkActionGroup::make([
          DeleteBulkAction::make(),
          ForceDeleteBulkAction::make(),
          RestoreBulkAction::make(),
        ]),
      ]);
  }
}
