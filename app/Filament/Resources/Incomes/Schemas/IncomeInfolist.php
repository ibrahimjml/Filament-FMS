<?php

namespace App\Filament\Resources\Incomes\Schemas;

use App\Models\Income;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\FontWeight;


class IncomeInfolist
{
  public static function configure(Schema $schema): Schema
  {
    return $schema
      ->components([
        Group::make()
          ->schema([
            Section::make(__('Income Information'))
              ->schema([
                Grid::make(2)
                  ->schema([
                    TextEntry::make('subcategory.sub_name')
                      ->label(__('Subcategory'))
                      ->getStateUsing(fn($record) => $record->subcategory?->name)
                      ->icon('heroicon-o-tag')
                      ->iconColor('warning')
                      ->weight(FontWeight::Bold),
                    TextEntry::make('subcategory.category.category_name')
                      ->label(__('Category'))
                      ->getStateUsing(fn($record) => $record->subcategory?->category?->name)
                      ->icon('heroicon-o-tag')
                      ->iconColor('warning')
                      ->weight(FontWeight::Bold),
                    TextEntry::make('client.full_name')
                      ->label(__('Client Name'))
                      ->icon('heroicon-o-user')
                      ->iconColor('info')
                      ->weight(FontWeight::Bold),
                    TextEntry::make('client.client_phone')
                      ->label(__('Client Phone'))
                      ->icon('heroicon-o-phone')
                      ->iconColor('primary')
                      ->weight(FontWeight::Bold),
                    TextEntry::make('client.email')
                      ->label(__('Client Email'))
                      ->icon('heroicon-o-envelope')
                      ->iconColor('primary')
                      ->weight(FontWeight::Bold),
                    TextEntry::make('amount')
                      ->label(__('Amount'))
                      ->prefix('$')
                      ->icon('heroicon-o-currency-dollar')
                      ->iconColor('primary')
                      ->formatStateUsing(fn($record) => number_format($record->amount))
                      ->weight(FontWeight::Bold),
                    TextEntry::make('discount_amount')
                      ->label(__('Discount'))
                      ->prefix('$')
                      ->icon('heroicon-o-currency-dollar')
                      ->iconColor('primary')
                      ->visible(fn(Income $record): bool => $record->discount_amount > 0)
                      ->weight(FontWeight::Bold),
                    TextEntry::make('final_amount')
                      ->label(__('Final Amount'))
                      ->prefix('$')
                      ->icon('heroicon-o-currency-dollar')
                      ->iconColor('primary')
                      ->visible(fn(Income $record): bool => $record->final_amount > 0)
                      ->formatStateUsing(fn($record) => number_format($record->final_amount))
                      ->weight(FontWeight::Bold),
                    TextEntry::make('total_paid')
                      ->label(__('Total Paid'))
                      ->prefix('$')
                      ->icon('heroicon-o-currency-dollar')
                      ->iconColor('primary')
                      ->formatStateUsing(fn($record) => number_format($record->total_paid))
                      ->weight(FontWeight::Bold),
                    TextEntry::make('remaining')
                      ->label(__('Remaining'))
                      ->prefix('$')
                      ->icon('heroicon-o-currency-dollar')
                      ->iconColor('primary')
                      ->formatStateUsing(fn($record) => number_format($record->remaining))
                      ->weight(FontWeight::Bold),
                  ])->columnSpanFull()
              ])->columnSpanFull(),
          ])->columnSpan(['lg' => 2]),
        Group::make()
          ->schema([
            Section::make(__('Payment Information'))
              ->schema([
                Grid::make(2)
                  ->schema([
                    TextEntry::make('status')
                      ->label(__('Status'))
                      ->icon(fn($state) => $state?->icon())
                      ->formatStateUsing(fn($state) => $state?->getLabel())
                      ->badge()
                      ->weight(FontWeight::Bold),
                    TextEntry::make('payment_type')
                      ->label(__('Payment Type'))
                      ->icon(fn($state) => $state?->icon())
                      ->badge()
                      ->formatStateUsing(fn($state) => $state?->getLabel())
                      ->weight(FontWeight::Bold),

                    TextEntry::make('next_payment')
                      ->label(__('Next Payment'))
                      ->date('m - d - Y')
                      ->badge()
                      ->icon('heroicon-m-clock')
                      ->weight(FontWeight::Bold)
                      ->placeholder('-'),
                    TextEntry::make('deleted_at')
                      ->label(__('Deleted At'))
                      ->date('m - d - Y')
                      ->visible(fn(Income $record): bool => $record->trashed()),
                    TextEntry::make('created_at')
                      ->label(__('Created At'))
                      ->date('m - d - Y')
                      ->icon('heroicon-m-calendar')
                      ->iconColor('primary')
                      ->weight(FontWeight::Bold)
                      ->placeholder('-'),
                    TextEntry::make('updated_at')
                      ->label(__('Updated At'))
                      ->date('m - d - Y')
                      ->weight(FontWeight::Bold)
                      ->icon('heroicon-m-calendar')
                      ->iconColor('info')
                      ->placeholder('-'),
                  ]),
              ]),
          ])->columnSpan(['lg' => 1]),
        Section::make(__('Additional Information'))
          ->schema([
            TextEntry::make('description')
              ->label(__('Description'))
              ->getStateUsing(fn($record) => $record->trans_description)
              ->html(),
          ])->columnSpanFull(),
      ])->columns(3);
  }
}
