<?php
namespace App\Enums;

use Filament\Support\Contracts\HasColor;

enum PaymentStatus:string implements HasColor
{
  case PAID = 'paid';
  case UNPAID = 'unpaid';
  case CANCELED = 'canceled';
 

      public function getLabel(): string
    {
        return __('messages.payment_status.' . $this->value);
    }
      public function getColor(): string|array|null
    {
      return match ($this) {
         self::PAID => 'success',
         self::UNPAID => 'danger',
         self::CANCELED => 'warning'
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