<?php
namespace App\Enums;

use Filament\Support\Contracts\HasColor;


enum IncomeStatus:string implements HasColor
{
  case PENDING = 'pending';
  case PARTIAL = 'partial';
  case COMPLETED = 'completed';
 

      public function getLabel(): string
    {
        return __('messages.income_status.' . $this->value);
    }
      public function getColor(): string|array|null
    {
      return match ($this) {
         self::PENDING => 'danger',
         self::PARTIAL => 'warning',
         self::COMPLETED => 'success'
      };
    }
    public function icon()
    {
       return match ($this) {
          self::PENDING => 'heroicon-m-pause-circle',
          self::PARTIAL => 'heroicon-m-clock',
          self::COMPLETED => 'heroicon-m-check-circle',
       };
    }
      
    
  }