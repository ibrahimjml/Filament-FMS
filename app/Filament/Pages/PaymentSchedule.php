<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;

class PaymentSchedule extends Page
{
    protected string $view = 'filament.pages.payment-schedule';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-date-range';
    protected static ?string $navigationLabel = 'Payments Schedule';
    public function getHeading(): ?string
  {
    return __('Payments Schedule');
  }
}
