<?php

namespace App\Filament\Resources\Outcomes\Pages;

use App\Filament\Resources\Outcomes\OutcomesResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditOutcomes extends EditRecord
{
    protected static string $resource = OutcomesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
