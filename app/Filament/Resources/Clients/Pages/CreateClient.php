<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class CreateClient extends CreateRecord
{
  protected static string $resource = ClientResource::class;
  protected array $translatedData = [];
  protected function mutateFormDataBeforeCreate(array $data): array
  {
    if (App::getLocale() !== 'en') {

      $this->translatedData = [
        'client_fname' => $data['client_fname'],
        'client_lname' => $data['client_lname'],
      ];
      $data['client_fname'] ??= $this->translatedData['client_fname'];
      $data['client_lname'] ??= $this->translatedData['client_lname'];

       return collect($data)
        ->except('client_type_id')
        ->toArray();
    }
    return $data;
  }
  protected function afterCreate(): void
  {
    /** @var \App\Models\Client $client */
    $client = $this->record;
     if ($typeId = $this->data['client_type_id'] ?? null) {
        $client->types()->attach($typeId);
    }
    
    if ( App::getLocale() !== 'en' && !empty($this->translatedData)) {
      $this->record->translations()->create([
        'lang_code'    => App::getLocale(),
        'client_fname' => $this->translatedData['client_fname'],
        'client_lname' => $this->translatedData['client_lname'],
      ]);
    }
  }
  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }
}
