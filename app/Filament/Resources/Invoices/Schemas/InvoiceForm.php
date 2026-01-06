<?php

namespace App\Filament\Resources\Invoices\Schemas;

use App\Enums\InvoiceStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class InvoiceForm
{
  public static function configure(Schema $schema): Schema
  {
    return $schema
      ->components([
        Section::make(__('Generate dynamic invoice'))
          ->schema([

            Grid::make(2)
              ->schema([
                Grid::make(1)
                  ->schema([
                    Fieldset::make(__('relation info'))
                      ->schema([


                        TextInput::make('invoice_number')
                          ->prefix('INV -')
                          ->required()
                          ->afterStateUpdated(function ($state, Set $set) {
                            if (! str_starts_with($state, 'INV-')) {
                              $set('invoice_number', 'INV-' . $state);
                            }
                          }),
                        Select::make('client_id')
                          ->label(__('Client'))
                          ->relationship('client', 'client_fname', modifyQueryUsing: fn($query) => $query->with('translation'))
                          ->getOptionLabelFromRecordUsing(fn($record) => $record->full_name)
                          ->searchable()
                          ->preload()
                          ->reactive()
                          ->required(),

                        Select::make('income_id')
                          ->label(__('Income'))
                          ->options(
                            fn(Get $get) =>
                            $get('client_id')
                              ? \App\Models\Income::where('client_id', $get('client_id'))
                              ->with(['client', 'subcategory'])
                              ->get()
                              ->mapWithKeys(fn($income) => [
                                $income->income_id => $income->client?->full_name
                                  . ' — ' . $income->subcategory?->name,
                              ])
                              : []
                          )
                          ->searchable()
                          ->reactive()
                          ->required(),

                        Select::make('payment_id')
                          ->label(__('Payment'))
                          ->options(
                            fn(Get $get) =>
                            $get('income_id')
                              ? \App\Models\Payment::where('income_id', $get('income_id'))
                              ->get()
                              ->mapWithKeys(fn($payment) => [
                                $payment->payment_id =>
                                '$ ' . number_format($payment->payment_amount)
                                  . ' — ' . $payment->status?->getLabel(),
                              ])
                              : []
                          )
                          ->searchable()
                          ->required(fn(Get $get) => $get('active_tab') === 0),
                      ])->columns(1),
                  ]),
                Grid::make(1)
                  ->schema([
                    Fieldset::make(__('invoice detail'))
                      ->schema([

                        Select::make('status')
                          ->label(__('Status'))
                          ->options(InvoiceStatus::options())
                          ->required(),

                        MarkdownEditor::make('description')
                          ->label(__('Description'))
                          ->hint(__('Editing in ') . strtoupper(app()->getLocale()))
                          ->maxHeight(200)
                          ->nullable(),
                      ])->columns(1),

                  ]),
              ]),
          ])->columnSpanFull()
      ]);
  }
}
