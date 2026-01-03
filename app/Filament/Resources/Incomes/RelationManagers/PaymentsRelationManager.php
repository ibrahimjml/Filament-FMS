<?php

namespace App\Filament\Resources\Incomes\RelationManagers;


use App\Filament\Resources\Payments\PaymentResource;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;


class PaymentsRelationManager extends RelationManager
{

    protected static string $relationship = 'payments';

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
            ->headerActions([
                CreateAction::make()
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
