<?php
namespace App\Enums;

use Filament\Support\Contracts\HasColor;

enum InvoiceStatus:string implements HasColor
{
  case PENDING = 'pending';
  case PARTIAL = 'partial';
  case PAID = 'paid';
  case OVERDUE = 'overdue';
 

      public function getLabel(): string
    {
        return __('messages.invoice_status.' . $this->value);
    }
      public function getColor(): string|array|null
    {
      return match ($this) {
         self::PENDING => 'warning',
         self::PAID => 'primary',
         self::PARTIAL => 'info',
         self::OVERDUE => 'danger'
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