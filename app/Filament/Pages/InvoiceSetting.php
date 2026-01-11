<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class InvoiceSetting extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Cog8Tooth;
    protected static ?int $navigationSort = 1;
    public static function getNavigationGroup(): string|UnitEnum|null
  {
    return __('Settings');
  }
      public static function getNavigationLabel(): string
    {
        return __('Invoice Setting');
    }
    protected string $view = 'filament.pages.invoice-setting';
}
