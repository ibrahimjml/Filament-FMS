<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditClient extends EditRecord
{
  protected static string $resource = ClientResource::class;
  protected function mutateFormDataBeforeFill(array $data): array
  {

    if (app()->getLocale() !== 'en' && $translation = $this->record->translation) {
      $data['client_fname'] = $translation->client_fname;
      $data['client_lname'] = $translation->client_lname;
    }
    $data['client_type_id'] = $this->record
      ->types()
      ->pluck('client_type.type_id')
      ->first();
    return $data;
  }

  protected function mutateFormDataBeforeSave(array $data): array
  {
    if (app()->getLocale() !== 'en') {
      unset($data['client_fname'], $data['client_lname']);
    }
    unset($data['client_type_id']);

    return $data;
  }

  protected function afterSave(): void
  {
    if (app()->getLocale() !== 'en') {
      $this->record->translations()->updateOrCreate(
        ['lang_code' => app()->getLocale()],
        [
          'client_fname' => $this->data['client_fname'],
          'client_lname' => $this->data['client_lname'],
        ]
      );
    }
    if (! empty($this->data['client_type_id'])) {
      $this->record->types()->sync([
        $this->data['client_type_id'],
      ]);
    } else {
      $this->record->types()->detach();
    }
  }
  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }
  protected function getHeaderActions(): array
  {
    return [
      DeleteAction::make(),
      ForceDeleteAction::make(),
      RestoreAction::make(),
    ];
  }
}
