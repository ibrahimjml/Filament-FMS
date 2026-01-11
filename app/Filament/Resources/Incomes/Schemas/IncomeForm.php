<?php

namespace App\Filament\Resources\Incomes\Schemas;

use App\Enums\CategoryType;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Clients\ClientResource;
use App\Filament\Resources\Discounts\DiscountResource;
use App\Filament\Resources\Subcategories\SubcategoryResource;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
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
    return $schema->components([
      self::generalGroup(),
      self::paymentGroup(),
    ])->columns(3);
  }

  protected static function generalGroup(): Group
  {
    return Group::make()
      ->schema([
        Section::make(__('General'))
          ->schema([
            self::incomeDetailsFieldset(),
            self::paymentInfoFieldset(),
            self::additionalInfoFieldset(),
          ])
          ->columns(1),
      ])
      ->columnSpan(['lg' => 2]);
  }

  protected static function paymentGroup(): Group
  {
    return Group::make()
      ->schema([
        Section::make(__('Payment Details'))
          ->schema([
            self::paymentTypeFieldset(),

            self::oneTimePaymentFieldset()
              ->visible(fn(callable $get) => $get('payment_type') === PaymentType::ONETIME->value),

            self::recurringPaymentFieldset()
              ->visible(fn(callable $get) => $get('payment_type') === PaymentType::RECURRING->value),
          ])
          ->columns(1),
      ])
      ->columnSpan(['lg' => 1]);
  }


  protected static function incomeDetailsFieldset(): Fieldset
  {
    return Fieldset::make(__('Income Details'))->schema([
      Select::make('client_id')
        ->label(__('Client'))
        ->relationship('client', 'client_fname', modifyQueryUsing: fn($query) => $query->with('translation'))
        ->getOptionLabelFromRecordUsing(fn($record) => $record->full_name)
        ->searchable(['client_fname', 'client_lname'])
        ->preload(true)
        ->required()
        ->createOptionForm(fn(Schema $schema) => ClientResource::form($schema)),
      Select::make('category_id')
        ->label(__('Category'))
        ->options(
          \App\Models\Category::where('category_type', CategoryType::INCOME)
            ->get()
            ->mapWithKeys(fn($category) => [
              $category->category_id => $category->name,
            ])
        )
        ->searchable()
        ->afterStateHydrated(function (callable $set, $record) {
          if ($record?->subcategory) {
            $set('category_id', $record->subcategory->category_id);
          }
        })
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
    ])->columns(1);
  }

  protected static function paymentInfoFieldset(): Fieldset
  {
    return Fieldset::make(__('Payment Information'))->schema([
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
    ])->columns(1);
  }

  protected static function additionalInfoFieldset(): Fieldset
  {
    return Fieldset::make(__('Additional Information'))->schema([
      MarkdownEditor::make('description')
        ->label(__('Description'))
        ->maxHeight(200)
        ->hint(__('translation-lang.editing_in', ['lang' => __('translation-lang.lang.' . app()->getLocale()),]))
        ->columnSpanFull(),
    ]);
  }

  protected static function paymentTypeFieldset(): Fieldset
  {
    return Fieldset::make(__('Choose a type for payment'))->schema([
      Radio::make('payment_type')
        ->label(__('Payment Type'))
        ->options(PaymentType::options())
        ->descriptions(PaymentType::descriptions())
        ->reactive()
        ->required(),
    ])->columns(1);
  }
  protected static function oneTimePaymentFieldset(): Fieldset
  {
    return Fieldset::make(__('OneTime Payment'))->schema([
      TextInput::make('payment_amount')
        ->label(__('Payment Amount'))
        ->prefix('$')
        ->required()
        ->maxValue(fn(callable $get) => $get('final_amount') > 0 ? $get('final_amount') : $get('amount'))
        ->numeric(),

      Select::make('payment_status')
        ->label(__('Payment Status'))
        ->options(PaymentStatus::options())
        ->reactive()
        ->required(),

      DatePicker::make('next_payment')
        ->label(__('Next Payment'))
        ->nullable()
        ->visible(fn(callable $get) => $get('payment_status') === PaymentStatus::UNPAID->value)
        ->afterOrEqual(fn() => now()),
    ])->columns(1);
  }
  protected static function recurringPaymentFieldset(): Fieldset
  {
    return Fieldset::make(__('Recurring Payments'))->schema([
      TextInput::make('recurring_count')
        ->label(__('Recurring Count'))
        ->hint(__('please choose the total recurring payment count'))
        ->visible(fn(callable $get) => $get('payment_type') === PaymentType::RECURRING->value)
        ->maxValue(10)
        ->minValue(1)
        ->reactive()
        ->numeric(),
  

      Repeater::make('payments')
        ->label(__('Payments'))
        ->schema([
          TextInput::make('payment_amount')->label(__('Payment Amount'))->prefix('$')->numeric()->required(),
          Select::make('status')
            ->label(__('Payment Status'))
            ->options(PaymentStatus::options())
            ->live()
            ->required(),
          DatePicker::make('next_payment')
            ->label(__('Next Payment'))
             ->disabled(fn ($get) => $get('status') === PaymentStatus::PAID->value)
            ->required(fn ($get) => $get('status') === PaymentStatus::UNPAID->value)
            ->afterOrEqual(fn() => now()),
        ])
        ->minItems(1)
        ->maxItems(10)
        ->visible(fn(callable $get) => $get('recurring_count') > 0 && $get('payment_type') === PaymentType::RECURRING->value)
    ])->columns(1)->live();
  }

}
