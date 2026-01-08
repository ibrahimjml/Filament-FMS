<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class InvoiceSetting extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Cog8Tooth;
    protected static string|\UnitEnum|null $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 1;
      public static function getNavigationLabel(): string
    {
        return __('Invoice Setting');
    }
    protected string $view = 'filament.pages.invoice-setting';
}
