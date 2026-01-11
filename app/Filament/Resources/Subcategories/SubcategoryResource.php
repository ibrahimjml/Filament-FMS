<?php

namespace App\Filament\Resources\Subcategories;

use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Subcategories\Pages\ManageSubcategories;
use App\Models\Subcategory;
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
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\App;
use UnitEnum;

class SubcategoryResource extends Resource
{
    protected static ?string $model = Subcategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;

    public static function getNavigationGroup(): string|UnitEnum|null
  {
    return __('Catalog');
  }

    protected static ?int $navigationSort = 2;

    public static function getPluralLabel(): string
    {
        return __('Subcategories');
    }
    public static function getModelLabel(): string
{
    return __('Subcategory');
}
    protected static ?string $recordTitleAttribute = 'sub_name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make(__('Select A Preferred Category'))
                    ->schema([
                        Select::make('category_id')
                            ->label(__('Category'))
                            ->relationship('category', 'category_name')
                            ->searchable()
                            ->preload(true)
                            ->required()
                            ->createOptionForm(fn(Schema $schema) => CategoryResource::form($schema)),
                    ])->columnSpanFull(),
                Fieldset::make(__('Create a new subcategory'))
                    ->schema([
                        TextInput::make('sub_name')
                            ->label(__('Subcategory Name'))
                            ->required()
                            ->hint(__('translation-lang.editing_in', ['lang' => __('translation-lang.lang.' . app()->getLocale()),]))


                    ])->columnSpanFull(),
            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('sub_name')
            ->columns([
                TextColumn::make('sub_name')
                    ->label(__('Subcategory Name'))
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where('sub_name', 'like', "%{$search}%")
                            ->orWhereHas('translations', function (Builder $q) use ($search) {
                                $q->where('sub_name', 'like', "%{$search}%");
                            });
                    })
                    ->getStateUsing(fn ($record) => $record->name),
                TextColumn::make('category.category_name')
                    ->label(__('Category'))
                    ->badge()
                    ->color('gray')
                    ->icon('heroicon-o-queue-list')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->getStateUsing(fn ($record) => $record->category->name),
                TextColumn::make('category.category_type')
                    ->label(__('Category Type'))
                    ->badge()
                    ->icon(fn($state) => $state?->icons())
                    ->sortable(),
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
            ->filters([
                TrashedFilter::make(),

                Filter::make('income')
                    ->label(__('Incomes'))
                    ->query(fn (Builder $query) => $query->income()),

                Filter::make('outcome')
                    ->label(__('Outcomes'))
                    ->query(fn (Builder $query) => $query->outcome()),
            ])

            ->recordActions([
                EditAction::make()
                    ->mutateRecordDataUsing(function (array $data, Subcategory $record): array {
                        if (App::getLocale() !== 'en' && $translation = $record->translation) {
                            $data['sub_name'] = $translation->sub_name;
                        }

                        return $data;
                    })
                    ->using(function (Subcategory $record, array $data): Subcategory {
                        if (App::getLocale() !== 'en') {
                            $translated = $data['sub_name'];
                            unset($data['sub_name']);

                            $record->update($data);

                            $record->translations()->updateOrCreate(
                                ['lang_code' => App::getLocale()],
                                ['sub_name' => $translated]
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
            'index' => ManageSubcategories::route('/'),
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
