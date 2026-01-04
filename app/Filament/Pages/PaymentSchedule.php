<?php

namespace App\Filament\Pages;

use App\Services\PaymentDueService;
use BackedEnum;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

class PaymentSchedule extends Page implements HasForms
{
  use InteractsWithForms;
  protected string $view = 'filament.pages.payment-schedule';
  protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-date-range';
  protected static ?string $navigationLabel = 'Payments Schedule';
  protected $listeners = ['paymentPriority' => '$refresh'];
  public function getHeading(): ?string
  {
    return __('Payments Schedule');
  }
  protected function getFormSchema(): array
  {
    $payment = app(PaymentDueService::class);

    return [
      Tabs::make('Payments')
        ->tabs([
          Tab::make(__('Upcoming Payments'))
            ->icon('heroicon-m-beaker')
            ->badge(fn() => $payment->upcoming()->count())
            ->badgeColor('warning')
            ->schema([
              Livewire::make('payments.list-payments', [
                'type' => 'upcoming',
              ])->key('payments-upcoming'),
            ]),

          Tab::make(__('Today Payments'))
            ->icon('heroicon-m-bell')
            ->badge(fn() => $payment->today()->count())
            ->badgeColor('success')
            ->schema([
              Livewire::make('payments.list-payments', [
                'type' => 'today',
              ])->key('payments-today'),
            ]),

          Tab::make(__('Outdated Payments'))
            ->icon('heroicon-m-clock')
            ->badge(fn() => $payment->overdue()->count())
            ->badgeColor('danger')
            ->schema([
              Livewire::make('payments.list-payments', [
                'type' => 'outdated',
              ])->key('payments-outdated'),
            ]),
          Tab::make(__('By Priority'))
            ->icon('heroicon-m-shield-exclamation')
            ->reactive()
            ->badgeColor('gray')
            ->schema([
              Livewire::make('payments.list-payments', [
                'type' => 'priority',
              ])->key('payments-byPriority'),
            ]),
        ]),
    ];
  }
}
