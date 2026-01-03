<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\App;

class ManageCategories extends ManageRecords
{
    protected static string $resource = CategoryResource::class;

    protected ?array $translatedData = [];

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('Create Category'))
                ->mutateDataUsing(function (array $data) {
                    if (App::getLocale() !== 'en') {
                        $this->translatedData = [
                            'category_name' => $data['category_name'],
                        ];

                        $data['category_name'] ??= $this->translatedData['category_name'];
                    }

                    return $data;
                })
                ->after(function ($record) {
                    if (App::getLocale() !== 'en' && ! empty($this->translatedData)) {
                        $record->translations()->create([
                            'lang_code' => App::getLocale(),
                            'category_name' => $this->translatedData['category_name'],
                        ]);
                    }
                }),
       ];
    }
}
