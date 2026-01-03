<?php

namespace App\Filament\Resources\Incomes\Pages;


use App\Filament\Resources\Incomes\IncomeResource;
use App\Filament\Resources\Payments\PaymentResource;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;


class IncomePayments extends ManageRelatedRecords
{
  protected static string|BackedEnum|null $navigationIcon = Heroicon::CurrencyDollar;
  protected static string $resource = IncomeResource::class;

  protected static string $relationship = 'payments';
  
        public static function getNavigationLabel(): string
    {
        return __('Payments');
    }
  public function form(Schema $schema): Schema
  {
     $paymentSchema = PaymentResource::form($schema);

    $existingComponents = $paymentSchema->getComponents(withActions: true, withHidden: true);

    $paymentSchema->components(array_merge([
        Hidden::make('income_id')
            ->default(fn($livewire) => $livewire->getOwnerRecord()?->income_id ?? $livewire->getOwnerRecord()?->id ?? null),
    ], $existingComponents));

    return $paymentSchema;
  }
  public function table(Table $table): Table
  {
    return PaymentResource::table($table)
      ->filters([
        TrashedFilter::make(),
      ])
      ->recordActions([
        EditAction::make(),
        
        DeleteAction::make(),
      ])
      ->headerActions([
        CreateAction::make()
      ]);
  }
}
