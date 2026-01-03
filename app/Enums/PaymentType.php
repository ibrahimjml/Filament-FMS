<?php
namespace App\Enums;


enum PaymentType:string 
{
  case ONETIME = 'onetime';
  case RECURRING = 'recurring';
 

      public function getLabel(): string
    {
        return __('messages.payment_type.' . $this->value);
    }
      public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [
                $case->value => $case->getLabel(),
            ])
            ->toArray();
    }
     public static function descriptions(): array
    {
        return [
            self::ONETIME->value  => __('Paid once with no future payments'),
            self::RECURRING->value => __('Payment repeats on a schedule'),
        ];
    }
    public function icon()
    {
       return match ($this) {
          self::ONETIME => 'heroicon-m-currency-dollar',
          self::RECURRING => 'heroicon-m-arrow-path',
       };
    }
  }