<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum DiscountType:string implements HasLabel, HasColor
{
    case RATE = 'rate';
    case FIXED = 'fixed';

      public function getLabel(): string
    {
        return __('messages.discount_type.' . $this->value);
    }
      public function getColor(): string|array|null
    {
      return match ($this) {
         self::RATE => 'primary',
         self::FIXED => 'danger'
      };
    }
    public function icons()
    {
       return match ($this) {
          self::RATE => 'heroicon-m-percent-badge',
          self::FIXED => 'heroicon-m-currency-dollar',
       };
    }
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [
                $case->value => $case->getLabel(),
            ])
            ->toArray();
    }
}
