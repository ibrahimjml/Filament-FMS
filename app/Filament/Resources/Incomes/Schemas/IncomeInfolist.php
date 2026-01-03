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
                      ->icon('heroicon-o-tag')
                      ->weight(FontWeight::Bold),
                    TextEntry::make('subcategory.category.category_name')
                      ->label(__('Category'))
                      ->icon('heroicon-o-tag')
                      ->weight(FontWeight::Bold),
                    TextEntry::make('client.full_name')
                      ->label(__('Client Name'))
                      ->icon('heroicon-o-user')
                      ->weight(FontWeight::Bold),
                    TextEntry::make('client.client_phone')
                      ->label(__('Client Phone'))
                      ->icon('heroicon-o-phone')
                      ->weight(FontWeight::Bold),
                    TextEntry::make('client.email')
                      ->label(__('Client Email'))
                      ->icon('heroicon-o-envelope')
                      ->weight(FontWeight::Bold),
                    TextEntry::make('amount')
                      ->label(__('Amount'))
                      ->prefix('$')
                      ->formatStateUsing(fn($record) => number_format($record->amount))
                      ->weight(FontWeight::Bold),
                    TextEntry::make('discount_amount')
                      ->prefix('$')
                      ->visible(fn(Income $record): bool => $record->discount_amount > 0)
                      ->weight(FontWeight::Bold),
                    TextEntry::make('final_amount')
                      ->prefix('$')
                      ->visible(fn(Income $record): bool => $record->final_amount > 0)
                      ->formatStateUsing(fn($record) => number_format($record->final_amount))
                      ->weight(FontWeight::Bold),
                    TextEntry::make('total_paid')
                      ->prefix('$')
                      ->formatStateUsing(fn($record) => number_format($record->total_paid))
                      ->weight(FontWeight::Bold),
                    TextEntry::make('remaining')
                      ->prefix('$')
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
                      ->badge()
                      ->weight(FontWeight::Bold),
                    TextEntry::make('payment_type')
                      ->label(__('Payment Type'))
                      ->icon(fn($state) => $state?->icon())
                      ->badge()
                      ->formatStateUsing(fn($state) => $state?->getLabel())
                      ->weight(FontWeight::Bold),

                    TextEntry::make('next_payment')
                      ->date('m / d / Y')
                      ->badge()
                      ->icon('heroicon-m-clock')
                      ->weight(FontWeight::Bold)
                      ->placeholder('-'),
                    TextEntry::make('deleted_at')
                      ->date('m / d / Y')
                      ->visible(fn(Income $record): bool => $record->trashed()),
                    TextEntry::make('created_at')
                      ->date('m / d / Y')
                      ->icon('heroicon-m-calendar')
                      ->placeholder('-'),
                    TextEntry::make('updated_at')
                      ->date('m / d / Y')
                      ->icon('heroicon-m-calendar')
                      ->placeholder('-'),
                  ]),
              ]),
          ])->columnSpan(['lg' => 1]),
        Section::make(__('Additional Information'))
          ->schema([
            TextEntry::make('description')
              ->html(),
          ])->columnSpanFull(),
      ])->columns(3);
  }
}
