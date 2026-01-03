<?php

namespace App\Filament\Exports;

use App\Models\Client;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class ClientExporter extends Exporter
{
    protected static ?string $model = Client::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('client_id'),
            ExportColumn::make('client_fname'),
            ExportColumn::make('client_phone'),
            ExportColumn::make('email'),
            ExportColumn::make('created_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your client export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
