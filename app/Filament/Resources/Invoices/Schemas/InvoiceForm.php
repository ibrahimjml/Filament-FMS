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
                    Fieldset::make(__('Relation Info'))
                      ->schema([


                        TextInput::make('invoice_number')
                          ->label(__('Invoice Number'))
                          ->prefix('INV -')
                          ->required()
                          ->afterStateUpdated(function ($state, Set $set) {
                            if (! str_starts_with($state, 'INV-')) {
                              $set('invoice_number', 'INV-' . $state);
                            }
                          }),
                        Select::make('client_id')
                          ->label(__('Client Name'))
                          ->relationship('client', 'client_fname', modifyQueryUsing: fn($query) => $query->with('translation'))
                          ->getOptionLabelFromRecordUsing(fn($record) => $record->full_name)
                          ->searchable()
                          ->preload()
                          ->reactive()
                          ->required(),

                        Select::make('income_id')
                          ->label(__('Incomes'))
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
                          ->label(__('Payments'))
                          ->multiple()
                          ->reactive()
                          ->searchable()
                          ->options(function (Get $get, $livewire) {
                            $incomeId  = $get('income_id');
                            $invoiceId = $livewire->record?->invoice_id;

                            if (! $incomeId) {
                              return [];
                            }

                            return \App\Models\Payment::where('income_id', $incomeId)
                              ->where(function ($query) use ($invoiceId) {
                                $query
                                  ->whereNull('invoice_id')               
                                  ->orWhere('invoice_id', $invoiceId); 
                              })
                              ->get()
                              ->mapWithKeys(fn($payment) => [
                                $payment->payment_id =>
                                '$ ' . number_format($payment->payment_amount)
                                  . ' — ' . $payment->status?->getLabel(),
                              ]);
                          })
                          ->required(),
                      ])->columns(1),
                  ]),
                Grid::make(1)
                  ->schema([
                    Fieldset::make(__('Invoice Detail'))
                      ->schema([

                        Select::make('status')
                          ->label(__('Status'))
                          ->options(InvoiceStatus::options())
                          ->required(),

                        MarkdownEditor::make('description')
                          ->label(__('Description'))
                          ->hint(__('translation-lang.editing_in', ['lang' => __('translation-lang.lang.' . app()->getLocale()),]))
                          ->maxHeight(200)
                          ->nullable(),
                      ])->columns(1),

                  ]),
              ]),
          ])->columnSpanFull()
      ]);
  }
}
