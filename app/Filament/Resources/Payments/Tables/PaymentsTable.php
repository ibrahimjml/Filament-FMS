<?php

namespace App\Filament\Resources\Payments\Tables;

use App\Models\Payment;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class PaymentsTable
{
  public static function configure(Table $table): Table
  {
    return $table
      ->reorderableColumns()
      ->columnManagerColumns(2)
      ->columns([
        TextColumn::make('payment_amount')
          ->money()
          ->sortable(),
        TextColumn::make('status')
          ->color(fn($state) => $state?->getColor())
          ->badge()
          ->searchable(),
        TextColumn::make('next_payment')
          ->label(__('Due Date'))
          ->date('m / d / Y')
          ->icon('heroicon-m-calendar')
          ->sortable(),
        TextColumn::make('paid_at')
          ->date('m / d / Y')
          ->icon('heroicon-m-calendar')
          ->sortable(),
        TextColumn::make('created_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
        TextColumn::make('updated_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
        TextColumn::make('deleted_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
          
            ])

      ->filters([
        TrashedFilter::make(),
      ])
      ->recordActions([
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
