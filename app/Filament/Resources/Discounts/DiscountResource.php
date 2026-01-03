<?php

namespace App\Filament\Resources\Discounts;

use App\Enums\DiscountType;
use App\Filament\Resources\Discounts\Pages\ManageDiscounts;
use App\Models\Discount;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DiscountResource extends Resource
{
    protected static ?string $model = Discount::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::PercentBadge;

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('discount details')
                    ->label(__('Discount Detials'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('Name'))
                            ->required(),
                        Select::make('type')
                            ->label(__('Type'))
                            ->options(DiscountType::options())
                            ->required()
                            ->reactive(),
                        TextInput::make('rate')
                            ->label(__('Rate'))
                            ->prefix('%')
                            ->visible(fn ($get) => $get('type') === DiscountType::RATE->value)
                            ->numeric()
                            ->maxValue(70),
                        TextInput::make('fixed_amount')
                            ->label(__('Fixed'))
                            ->prefix('$')
                            ->visible(fn ($get) => $get('type') === DiscountType::FIXED->value)
                            ->numeric(),
                    ])->columns(1)->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable(),
                TextColumn::make('rate')
                    ->label(__('Rate'))
                    ->prefix('%')
                    ->sortable(),
                TextColumn::make('fixed_amount')
                    ->label(__('Fixed'))
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state)),
                TextColumn::make('type')
                    ->label(__('Type'))
                    ->badge()
                    ->icon(fn ($state) => $state?->icons())
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([

            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageDiscounts::route('/'),
        ];
    }
}
