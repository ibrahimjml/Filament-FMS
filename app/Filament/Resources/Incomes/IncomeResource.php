<?php

namespace App\Filament\Resources\Incomes;

use App\Filament\Resources\Incomes\Pages\CreateIncome;
use App\Filament\Resources\Incomes\Pages\EditIncome;
use App\Filament\Resources\Incomes\Pages\IncomePayments;
use App\Filament\Resources\Incomes\Pages\ListIncomes;
use App\Filament\Resources\Incomes\Pages\ViewIncome;
use App\Filament\Resources\Incomes\RelationManagers\PaymentsRelationManager;
use App\Filament\Resources\Incomes\Schemas\IncomeForm;
use App\Filament\Resources\Incomes\Schemas\IncomeInfolist;
use App\Filament\Resources\Incomes\Tables\IncomesTable;
use App\Models\Income;
use BackedEnum;
use Filament\Resources\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class IncomeResource extends Resource
{
    protected static ?string $model = Income::class;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArrowTrendingUp;
   public static function getNavigationGroup(): string|UnitEnum|null
  {
    return __('Finance');
  }
    protected static ?int $navigationSort = 1;
      public static function getNavigationLabel(): string
    {
        return __('Incomes');
    }
      public static function getPluralLabel(): string
    {
        return __('Incomes');
    }
  public static function getModelLabel(): string
{
    return __('Income');
}
    public static function form(Schema $schema): Schema
    {
        return IncomeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return IncomeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IncomesTable::configure($table);
    }

   public static function getRelations(): array
    {
        return [
            RelationGroup::make(__('Payments'), [
                PaymentsRelationManager::class,
            ])
                ->icon('heroicon-o-currency-dollar'),

        ];
    }
    public static function getRecordSubNavigation(Page $page): array
    {
      return $page->generateNavigationItems([
            ViewIncome::class,
            EditIncome::class,
            IncomePayments::class,

        ]);
    }
    public static function getPages(): array
    {
        return [
            'index' => ListIncomes::route('/'),
            'create' => CreateIncome::route('/create'),
            'view' => ViewIncome::route('/{record}'),
            'edit' => EditIncome::route('/{record}/edit'),
            'payments' => IncomePayments::route('/{record}/payments'),
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
