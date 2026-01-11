<?php

namespace App\Filament\Resources\Outcomes;

use App\Filament\Resources\Outcomes\Pages\CreateOutcomes;
use App\Filament\Resources\Outcomes\Pages\EditOutcomes;
use App\Filament\Resources\Outcomes\Pages\ListOutcomes;
use App\Filament\Resources\Outcomes\Schemas\OutcomesForm;
use App\Filament\Resources\Outcomes\Tables\OutcomesTable;
use App\Models\Outcome;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class OutcomesResource extends Resource
{
    protected static ?string $model = Outcome::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArrowTrendingDown;
  public static function getNavigationGroup(): string|UnitEnum|null
  {
    return __('Finance');
  }
    protected static ?int $navigationSort = 2;
      public static function getNavigationLabel(): string
  {
    return __('Outcomes');
  }
    protected static ?string $recordTitleAttribute = 'outcome';

    public static function form(Schema $schema): Schema
    {
        return OutcomesForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OutcomesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOutcomes::route('/'),
            'create' => CreateOutcomes::route('/create'),
            'edit' => EditOutcomes::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
