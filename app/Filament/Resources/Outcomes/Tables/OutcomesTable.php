<?php

namespace App\Filament\Resources\Outcomes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class OutcomesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subcategory')
                    ->label(__('Subcategory'))
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(fn($record) => $record->subcategory->name),
                TextColumn::make('category')
                    ->label(__('Category'))
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(fn ($record) =>$record->category?->name),
                TextColumn::make('amount')
                    ->label(__('Amount'))
                    ->money()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('description')
                    ->label(__('Description'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->date()
                    ->sortable()
                    ->searchable(),
                
    
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
