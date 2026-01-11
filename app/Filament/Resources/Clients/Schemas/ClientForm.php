<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make(__('Full Name'))
                    ->schema([
                        TextInput::make('client_fname')
                            ->label(__('Client First'))
                            ->required()
                            ->hint(__('translation-lang.editing_in', ['lang' => __('translation-lang.lang.' . app()->getLocale()),])),
                        TextInput::make('client_lname')
                            ->label(__('Client Last'))
                            ->required()
                            ->hint(__('translation-lang.editing_in', ['lang' => __('translation-lang.lang.' . app()->getLocale()),])),
                    ])->columns(1),
                Fieldset::make(__('choose a preffered type'))
                    ->schema([
                        Select::make('types')
                            ->label(__('Type'))
                            ->required()
                            ->relationship('types', 'type_name')
                            ->multiple()
                            ->searchable()
                            ->preload(true)
                            ->createOptionForm([
                                TextInput::make('type_name')
                                    ->label(__('Type Name'))
                                    ->required(),
                            ])
                            ->createOptionAction(fn (Action $action) => $action
                                ->icon('heroicon-m-plus')
                                ->label(__('Type Name'))
                            ),
                    ])->columns(1),
                Fieldset::make(__('Info about client'))
                    ->schema([
                        TextInput::make('client_phone')
                            ->label(__('Phone'))
                            ->required()
                            ->tel(),
                        TextInput::make('email')
                            ->label(__('Email'))
                            ->required()
                            ->email(),
                    ])->columns(1),

            ]);
    }
}
