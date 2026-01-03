<?php

namespace App\Filament\Resources\Outcomes\Schemas;

use App\Enums\CategoryType;
use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Subcategories\SubcategoryResource;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OutcomesForm
{
  public static function configure(Schema $schema): Schema
  {
    return $schema
      ->components([
        Section::make(__('General'))->schema([
          Grid::make(2)->schema([
            Select::make('category.category_id')
              ->label(__('Category'))
              ->options(
                \App\Models\Category::where('category_type', CategoryType::OUTCOME)
                  ->pluck('category_name', 'category_id')
              )
              ->searchable()
              ->prefixIcon('heroicon-o-tag')
              ->prefixIconColor('primary')
              ->required()
              ->createOptionForm(fn(Schema $schema) => CategoryResource::form($schema)),
            Select::make('subcategory_id')
              ->label(__('Subcategory'))
              ->relationship('subcategory', 'sub_name', modifyQueryUsing: fn($query) => $query->whereHas('category', fn($q) => $q->where('category_type', CategoryType::OUTCOME)))
              ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
              ->searchable('name')
              ->preload()
              ->prefixIcon('heroicon-o-tag')
              ->prefixIconColor('primary')
              ->required()
              ->createOptionForm(fn(Schema $schema) => SubcategoryResource::form($schema)),
          ]),
          Grid::make(2)->schema([
            TextInput::make('amount')
              ->label(__('Amount'))
              ->prefixIcon('heroicon-o-currency-dollar')
              ->prefixIconColor('primary')
              ->numeric()
              ->required(),
            MarkdownEditor::make('description')
              ->label(__('Description'))
              ->hint(__('Editing in ') . strtoupper(app()->getLocale()))
              ->maxHeight(200)
              ->nullable(),
          ]),
        ])->columnSpanFull(),
      ]);
  }
}
