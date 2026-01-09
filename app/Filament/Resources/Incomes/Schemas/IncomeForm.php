<?php

namespace App\Filament\Resources\Incomes\Schemas;

use App\Enums\CategoryType;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Clients\ClientResource;
use App\Filament\Resources\Discounts\DiscountResource;
use App\Filament\Resources\Subcategories\SubcategoryResource;
use App\Models\Payment;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;


class IncomeForm
{
  public static function configure(Schema $schema): Schema
  {
    return $schema
      ->components([
        Group::make()
          ->schema([
            Section::make(__('General'))
              ->schema([
                Fieldset::make('income_details')
                  ->label(__('Income Details'))
                  ->schema([
                    Select::make('client_id')
                      ->label(__('Client'))
                      ->relationship('client', 'client_fname', modifyQueryUsing: fn($query) => $query->with('translation'))
                      ->getOptionLabelFromRecordUsing(fn($record) => $record->full_name)
                      ->searchable(['client_fname', 'client_lname'])
                      ->preload(true)
                      ->required()
                      ->createOptionForm(fn(Schema $schema) => ClientResource::form($schema)),
                    Select::make('subcategory.category.category_id')
                      ->label(__('Category'))
                      ->options(
                        \App\Models\Category::where('category_type', CategoryType::INCOME)
                          ->pluck('category_name', 'category_id')
                      )
                      ->searchable()
                      ->required()
                      ->createOptionForm(fn(Schema $schema) => CategoryResource::form($schema)),
                    Select::make('subcategory_id')
                      ->label(__('Subcategories'))
                      ->relationship('subcategory', 'sub_name', modifyQueryUsing: fn($query) => $query->whereHas('category', fn($q) => $q->where('category_type', CategoryType::INCOME)))
                      ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
                      ->searchable('name')
                      ->preload(true)
                      ->required()
                      ->createOptionForm(fn(Schema $schema) => SubcategoryResource::form($schema)),
                  ])->columns(1),
                Fieldset::make('payment_info')
                  ->label(__('Payment Information'))
                  ->schema([
                    TextInput::make('amount')
                      ->label(__('Amount'))
                      ->required()
                      ->prefix('$')
                      ->reactive()
                      ->afterStateUpdated(function (callable $set, callable $get) {
                        recalcFinalAmount($set, $get);
                      }),
                    Select::make('discount_id')
                      ->label(__('Discount'))
                      ->relationship('discount', 'name')
                      ->getOptionLabelFromRecordUsing(fn($record) => $record->display_label)
                      ->searchable()
                      ->preload(true)
                      ->reactive()
                      ->createOptionForm(fn(Schema $schema) => DiscountResource::form($schema))
                      ->afterStateUpdated(function (callable $set, callable $get) {
                        recalcFinalAmount($set, $get);
                      }),
                    TextEntry::make('final_amount')
                      ->label(__('Final Amount'))
                      ->prefix('$')
                      ->visible(fn(callable $get) => $get('discount_id') !== null)
                      ->getStateUsing(function (callable $set, callable $get) {
                        recalcFinalAmount($set, $get);
                      }),
                    TextInput::make('payments.payment_amount')
                      ->label(__('Payment Amount'))
                      ->prefix('$')
                      ->required()
                      ->maxValue(fn(callable $get) => $get('amount'))
                      ->numeric(),
                  ])->columns(1),
                Fieldset::make('additional_info')
                  ->label(__('Additional Information'))
                  ->schema([
                    RichEditor::make('description')
                      ->hint(strtoupper(app()->getLocale()))
                      ->columnSpanFull(),
                  ])
              ])->columns(1),
          ])->columnSpan(['lg' => 2]),
        Group::make()
          ->schema([
            Section::make(__('payment details'))
              ->schema([
                Fieldset::make('payment_details')
                  ->label(__('Choose a type for payment.'))
                  ->schema([
                    Radio::make('payment_type')
                      ->label(__('Payment Type'))
                      ->options(PaymentType::options())
                      ->descriptions(PaymentType::descriptions())
                      ->reactive()
                      ->required(),
                  ])->columns(1),
                TextInput::make('recurring_count')
                  ->label(__('Recurring Count'))
                  ->hint(__('please choose the total recurring payment count'))
                  ->visible(fn(callable $get) => $get('payment_type') === PaymentType::RECURRING->value)
                  ->maxValue(10)
                  ->minValue(1)
                  ->numeric(),
                Select::make('payments.status')
                  ->label(__('Payment Status'))
                  ->options(PaymentStatus::options())
                  ->reactive()
                  ->required(),
                DatePicker::make('next_payment')
                  ->label(__('Next Payment'))
                  ->nullable()
                  ->visible(fn(callable $get) => $get('payments.status') === PaymentStatus::UNPAID->value)
                  ->afterOrEqual(fn() => now()),
              ])->columns(1),
          ])->columnSpan(['lg' => 1]),
      ])->columns(3);
  }
}
