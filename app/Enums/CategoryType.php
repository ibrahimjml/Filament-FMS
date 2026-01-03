<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CategoryType:string implements HasColor, HasLabel
{
    case INCOME = 'income';
    case OUTCOME = 'outcome';

      public function getLabel(): string
    {
        return __('messages.category_type.' . $this->value);
    }
        public function getColor(): string|array|null
    {
      return match ($this) {
         self::INCOME => 'primary',
         self::OUTCOME => 'danger'
      };
    }
    public function icons()
    {
       return match ($this) {
          self::INCOME => 'heroicon-m-arrow-trending-up',
          self::OUTCOME => 'heroicon-m-arrow-trending-down'
       };
    }
  }
