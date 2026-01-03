<?php

namespace App\Filament\Resources\Clients\Tables;

use App\Filament\Exports\ClientExporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label(__('Full Name'))
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereRaw(
                            "CONCAT(client_fname, ' ', client_lname) LIKE ?", ["%{$search}%"]
                        )->orWhereHas('translations', function ($q) use ($search) {
                            $q->whereRaw("CONCAT(client_fname, ' ', client_lname) LIKE ?", ["%{$search}%"]);

                        });
                    }),
                TextColumn::make('types.type_name')
                    ->label(__('Type'))                
                    ->badge()
                    ->icon('heroicon-m-user-circle')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('client_phone')
                    ->label(__('Phone'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('email')
                    ->label(__('Email'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->headerActions([
              ExportAction::make()
                ->icon('heroicon-m-arrow-up-tray')
                ->label(__('Export'))
                ->exporter(ClientExporter::class)
            ])
            ->recordActions([
                DeleteAction::make(),
                EditAction::make(),
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
}
