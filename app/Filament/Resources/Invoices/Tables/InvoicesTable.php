<?php

namespace App\Filament\Resources\Invoices\Tables;

use App\Filament\Resources\Invoices\InvoiceResource;
use App\Models\Invoice;
use Dom\Text;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
              TextColumn::make('client_id')
                  ->label(__('Client Name'))
                  ->state(fn($record) => $record->client?->full_name)
                  ->icon('heroicon-m-user-circle')
                  ->searchable()
                  ->sortable(),
                  TextColumn::make('income_id')
                  ->label(__('Category'))
                  ->state(fn($record) => $record->income?->subcategory?->category->name . ' - ' . $record->income?->subcategory?->name)
                  ->badge()
                  ->color('gray')
                  ->icon('heroicon-m-tag')
                  ->searchable()
                  ->sortable(),
                  TextColumn::make('amount')
                      ->label(__('amount'))
                      ->money()
                      ->sortable(),
                TextColumn::make('payment_amount')
                    ->label(__('paid'))
                    ->state(fn($record) => $record->payment?->payment_amount)
                    ->money()
                    ->sortable(),
                    TextColumn::make('status')
                        ->label(__('status'))
                        ->badge()
                        ->color(fn($record) => $record->status?->getColor())
                        ->state(fn($record) => $record->status?->getLabel())
                        ->searchable(),
                    TextColumn::make('description')
                        ->label(__('description'))
                        ->state(fn($record) => $record->description ?? 'N/A')
                        ->html(),
                TextColumn::make('issue_date')
                    ->label(__('issue date'))
                    ->icon('heroicon-o-calendar')
                    ->date()
                    ->sortable(),
                TextColumn::make('due_date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                ViewAction::make()
                    ->hiddenLabel()
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn (Invoice $record) => InvoiceResource::getUrl('view', ['record' => $record])),
                EditAction::make()
                  ->hiddenLabel(),
                Action::make('download')
                    ->hiddenLabel()
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->url(fn(Invoice $record) => route('invoice.pdf',['invoice'=>$record])),
            
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
