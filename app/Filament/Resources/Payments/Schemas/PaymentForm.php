<?php

namespace App\Filament\Resources\Payments\Schemas;

use App\Enums\PaymentStatus;
use App\Models\Income;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PaymentForm
{
  public static function configure(Schema $schema): Schema
  {
    return $schema
      ->components([

        TextInput::make('payment_amount')
          ->required()
          ->numeric()
          ->reactive()
          ->default(0.0)
          ->maxValue(function (callable $get) {
                  $incomeId = $get('income_id');
                  if (! $incomeId) {
                    return null; 
                  }
                  $income = Income::find($incomeId);

                  return $income->remaining ;
                })
          ->prefix('$')
          ->disabled(function (callable $get) {
                  $incomeId = $get('income_id');
                  if (! $incomeId) {
                    return false;
                  }
                  $income = Income::find($incomeId);
                  return $income->remaining <= 0 ;
                }),
        Select::make('status')
          ->options(PaymentStatus::options())
          ->reactive()
          ->required(),
        RichEditor::make('description')
          ->columnSpanFull(),
        DatePicker::make('next_payment')
          ->label(__('Next Payment Date'))
          ->nullable()
          ->visible(fn(callable $get) => $get('status') === PaymentStatus::UNPAID->value)
          ->afterOrEqual(today()),
        DatePicker::make('paid_at')
          ->label(__('Paid At'))
          ->nullable()
          ->afterOrEqual(today())
          ->visible(fn(callable $get) => $get('status') === PaymentStatus::PAID->value),
      ]);
  }
}
