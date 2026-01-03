<?php

namespace App\Filament\Resources\Subcategories\Pages;


use App\Filament\Resources\Subcategories\SubcategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\App;

class ManageSubcategories extends ManageRecords
{
  protected static string $resource = SubcategoryResource::class;
  protected ?array $translatedData = [];
  protected function getHeaderActions(): array
  {
    return [
      CreateAction::make('create_subcategory')
      ->label(__('Create Subcategory'))
        ->mutateDataUsing(function (array $data) {
          if (App::getLocale() !== 'en') {
            $this->translatedData = [
              'sub_name' => $data['sub_name'],
            ];

            $data['sub_name'] ??= $this->translatedData['sub_name'];
          }

          return $data;
        })
        ->after(function ($record) {
          if (App::getLocale() !== 'en' && ! empty($this->translatedData)) {
            $record->translations()->create([
              'lang_code' => App::getLocale(),
              'sub_name'  => $this->translatedData['sub_name'],
            ]);
          }
        }),
    ];
  }

}
