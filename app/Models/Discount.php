<?php

namespace App\Models;

use App\Enums\DiscountType;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
  protected $table = 'discounts';
  protected $primaryKey = 'discount_id';
  protected $fillable = ['name','rate','fixed_amount','type'];
  protected $casts = [
    'type' => DiscountType::class,
     'fixed_amount' => 'decimal:2',
     'rate' => 'decimal:2'
  ];
public function getDisplayLabelAttribute(): string
{
    return match ($this->type) {
        DiscountType::RATE  => "{$this->name} - ({$this->type->getLabel()}) ~ ({$this->rate}%)",
        DiscountType::FIXED => "{$this->name} - ({$this->type->getLabel()}) ~ ({$this->fixed_amount})"
    };
}
  public function apply( $amount)
{
    return match ($this->type) {
        DiscountType::RATE  => max(0, $amount - ($amount * ($this->rate / 100))),
        DiscountType::FIXED => max(0, $amount - $this->fixed_amount),
    };
}
}
