<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Profile extends Page
{
    protected string $view = 'filament.pages.profile';

      public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

}
