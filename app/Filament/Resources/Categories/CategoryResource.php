<?php

namespace App\Filament\Resources\Categories;

use App\Enums\CategoryType;
use App\Filament\Resources\Categories\Pages\ManageCategories;
use App\Models\Category;
use BackedEnum;
use Filament\Actions\Action;
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
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\Rules\Enum;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 1;

    public static function getPluralLabel(): string
    {
        return __('Categories');
    }

    protected static ?string $recordTitleAttribute = 'category_name';

    public ?string $activeTab = null;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('new category')
                    ->schema([
                        TextInput::make('category_name')
                            ->label(__('Category Name'))
                            ->required(),
                    ])->columnSpanFull(),
                Fieldset::make('select category type')
                    ->schema([
                        Select::make('category_type')
                            ->label(__('Category Type'))
                            ->options(CategoryType::class)
                            ->searchable()
                            ->preload(true)
                            ->rules(['required', new Enum(CategoryType::class)]),
                    ])->columnSpanFull(),
            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('category_name')
            ->columns([
                TextColumn::make('category_name')
                    ->label(__('Category Name'))
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where('category_name', 'like', "%{$search}%")
                            ->orWhereHas('translations', function (Builder $q) use ($search) {
                                $q->where('category_name', 'like', "%{$search}%");
                            });
                    })
                    ->getStateUsing(fn ($record) => $record->name),
                TextColumn::make('subcategories.sub_name')
                    ->label(__('Subcategories'))
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->getStateUsing(fn ($record) => $record->subcategories->map(fn ($q) => $q->name)),

                TextColumn::make('category_type')
                    ->label(__('Type'))
                    ->badge()
                    ->icon(fn ($state) => $state?->icons()),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                Action::make('all')
                    ->label(__('All'))
                    ->color(fn ($livewire) => $livewire->activeTab === null ? 'primary' : 'gray')
                    ->outlined(true)
                    ->action(fn ($livewire) => $livewire->activeTab = null),

                Action::make('income')
                    ->label(__('Income'))
                    ->color(fn ($livewire) => $livewire->activeTab === 'income' ? 'primary' : 'gray')
                    ->icon('heroicon-m-arrow-trending-up')
                    ->badge(fn () => Category::where('category_type', CategoryType::INCOME)->count())
                    ->badgeColor('primary')
                    ->outlined(true)
                    ->action(fn ($livewire) => $livewire->activeTab = 'income'),

                Action::make('outcome')
                    ->label(__('Outcome'))
                    ->color(fn ($livewire) => $livewire->activeTab === 'outcome' ? 'primary' : 'gray')
                    ->icon('heroicon-m-arrow-trending-down')
                     ->badge(fn () => Category::where('category_type', CategoryType::OUTCOME)->count())
                     ->badgeColor('danger')
                    ->outlined(true)
                    ->action(fn ($livewire) => $livewire->activeTab = 'outcome'),
            ])
            ->modifyQueryUsing(function (Builder $query, $livewire) {
                if ($livewire->activeTab) {
                    $query->where('category_type', $livewire->activeTab);
                }
            })
             ->groups([
                Group::make('category_name')
                    ->label(__('Category Name')),
                Group::make('category_type')
                    ->label(__('Type')),
                Group::make('created_at')
                    ->label(__('Created At'))
                    ->date(),
            ])
            ->defaultSort('category_id', 'desc')
           ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->mutateRecordDataUsing(function (array $data, Category $record): array {
                        if (App::getLocale() !== 'en' && $translation = $record->translation) {
                            $data['category_name'] = $translation->category_name;
                        }

                        return $data;
                    })
                    ->using(function (Category $record, array $data): Category {
                        if (App::getLocale() !== 'en') {
                            $translated = $data['category_name'];
                            unset($data['category_name']);

                            $record->update($data);

                            $record->translations()->updateOrCreate(
                                ['lang_code' => App::getLocale()],
                                ['category_name' => $translated]
                            );

                            return $record;
                        }

                        $record->update($data);

                        return $record;
                    }),
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
            'index' => ManageCategories::route('/'),
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
