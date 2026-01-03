<?php

namespace App\Filament\Resources\Incomes\Tables;


use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Grouping\Group;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\ForceDeleteBulkAction;
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
          ])->space(1),

          Stack::make([
            TextColumn::make('amount')
              ->weight(FontWeight::SemiBold)
              ->state(fn($record) => __('Amount') . ' : ' . number_format($record->amount, 2))
              ->icon('heroicon-m-currency-dollar')
              ->iconColor('primary')
              ->sortable(),
            TextColumn::make('final_amount')
              ->money()
              ->state(fn($record) => __('Final') . ' : ' . number_format($record->final_amount, 2))
              ->weight(FontWeight::SemiBold)
              ->icon('heroicon-m-currency-dollar')
              ->iconColor('primary')
              ->sortable(),
          ]),

          Stack::make([
            TextColumn::make('total_paid')
              ->money()
              ->icon('heroicon-m-currency-dollar')
              ->iconColor('primary')
              ->weight(FontWeight::SemiBold)
              ->state(fn($record) => __('Paid') . ' : ' . number_format($record->total_paid, 2)),
            TextColumn::make('remaining')
              ->money()
              ->weight(FontWeight::SemiBold)
              ->icon('heroicon-m-currency-dollar')
              ->iconColor('primary')
              ->state(fn($record) => __('Remaining') . ' : ' . number_format($record->remaining, 2))

          ]),

          Stack::make([
            TextColumn::make('status')
              ->icon(fn($state) => $state?->icon())
              ->badge()
              ->sortable()
              ->searchable(),
            TextColumn::make('payment_type')
              ->icon(fn($state) => $state?->icon())
              ->badge()
              ->searchable(),

          ])->extraAttributes(['class' => 'flex flex-row gap-2 border-b dark:border-b-gray-200/20 pb-2']),

          Stack::make([
            TextColumn::make('next_payment')
              ->date(fn($state) => $state?->format('m-d-Y'))
              ->icon('heroicon-m-calendar')
              ->iconColor('primary')
              ->prefix('Due date: ')
              ->sortable()
              ->extraAttributes(['class' => 'text-green-400']),
            TextColumn::make('created_at')
              ->date(fn($state) => $state?->format('m-d-Y'))
              ->icon('heroicon-m-plus-circle')
              ->iconColor('warning')
              ->prefix('Created: ')
              ->sortable(),
            TextColumn::make('updated_at')
              ->date(fn($state) => $state?->format('m-d-Y'))
              ->icon('heroicon-m-pencil-square')
              ->iconColor('info')
              ->prefix('Edited: ')
              ->sortable(),
            TextColumn::make('deleted_at')
              ->date(fn($state) => $state?->format('m/d/Y'))
              ->sortable()
              ->toggleable(isToggledHiddenByDefault: true),
          ]),
        ])->extraAttributes(['class' => 'flex gap-3 '])
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

        ViewAction::make(),
        EditAction::make(),
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
