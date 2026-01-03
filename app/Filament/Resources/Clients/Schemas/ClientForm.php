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
                Fieldset::make('full name')
                    ->label(__('Full Name'))
                    ->schema([
                        TextInput::make('client_fname')
                            ->label(__('Client First'))
                            ->required()
                            ->hint(strtoupper(app()->getLocale())),
                        TextInput::make('client_lname')
                            ->label(__('Client Last'))
                            ->required()
                            ->hint(strtoupper(app()->getLocale())),
                    ])->columns(1),
                Fieldset::make('client type')
                    ->label(__('client type'))
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
                Fieldset::make('info')
                    ->label(__('info'))
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
