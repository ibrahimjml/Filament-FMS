<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Profile;
use App\Http\Middleware\SwitchLocale;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Filament\Navigation\UserMenuItem;

class AdminPanelProvider extends PanelProvider
{
  public function panel(Panel $panel): Panel
  {
    return $panel
      ->default()
      ->id('admin')
      ->path('/')
      ->login()
      ->sidebarFullyCollapsibleOnDesktop()
      ->brandName(__('Management System'))
      ->databaseNotifications()
      ->userMenuItems([
        'profile' => Action::make('profile')
          ->label('Profile')
          ->icon('heroicon-o-user')
          ->url(fn() => Profile::getUrl()),
      ])
      ->viteTheme('resources/css/filament/admin/theme.css')
      ->colors([
        'primary' => Color::Green,
        'danger'  => Color::Red
      ])
      ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
      ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
      ->pages([
        Dashboard::class,
      ])
      ->renderHook(
        PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
        fn(): string => Blade::render("@livewire('LanguageSwitcher')")
      )
      ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')

      ->middleware([
        EncryptCookies::class,
        AddQueuedCookiesToResponse::class,
        StartSession::class,
        AuthenticateSession::class,
        ShareErrorsFromSession::class,
        VerifyCsrfToken::class,
        SubstituteBindings::class,
        DisableBladeIconComponents::class,
        DispatchServingFilamentEvent::class,
        SwitchLocale::class,
      ])
      ->authMiddleware([
        Authenticate::class,
      ]);
  }
}
