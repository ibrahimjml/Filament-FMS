<?php

namespace App\Models;

use App\Enums\IncomeStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Traits\IncomeTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Income extends Model
{
  use HasFactory, SoftDeletes, IncomeTranslations;

  protected $table = 'incomes';

  protected $primaryKey = 'income_id';

  protected $dates = [
    'deleted_at',
    'created_at',
    'updated_at',
  ];

  protected $casts = [
    'status' => IncomeStatus::class,
    'payment_type' => PaymentType::class,
    'amount' => 'decimal:2',
    'discount_amount' => 'decimal:2',
    'final_amount' => 'decimal:2',
    'next_payment' => 'date',
    'date' => 'date',
  ];
protected $attributes = [
  'final_amount' => 0.00
];
  protected $fillable = [
    'client_id',
    'discount_id',
    'subcategory_id',
    'amount',
    'discount_amount',
    'final_amount',
    'status',
    'payment_type',
    'description',
    'next_payment',
    'date',
  ];

  public function client(): BelongsTo
  {
    return $this->belongsTo(Client::class, 'client_id');
  }

  public function subcategory(): BelongsTo
  {
    return $this->belongsTo(Subcategory::class, 'subcategory_id');
  }
  // public function category(): BelongsTo
  // {
  //   return $this->subcategory()->category();
  // }

  public function discount(): BelongsTo
  {
    return $this->belongsTo(Discount::class, 'discount_id');
  }

  public function payments(): HasMany
  {
    return $this->hasMany(Payment::class, 'income_id');
  }
  public function getTotalPaidAttribute(): float
  {
    return (float) $this->paidPayments()->sum('payment_amount');
  }
  public function getRemainingAttribute()
  {
    $amount = $this->final_amount > 0 ? $this->final_amount : $this->amount;
    return max(0, $amount - $this->total_paid);
  }
  public function getDiscountAmountAttribute()
  {
    if($this->final_amount > 0){
        return max(0,($this->amount ?? 0) - ($this->final_amount ?? 0));
    }
    return 0;
  }
  public function paidPayments()
  {
    return $this->payments()->where('status', PaymentStatus::PAID);
  }
  public function unpaidPayments()
  {
    return $this->payments()->where('status', PaymentStatus::UNPAID);
  }
  public function getNextPaymentAmountAttribute()
{
    $next = $this->unpaidPayments->first();
    return $next?->payment_amount ?? 0;
}

  public function isRecurring(): bool
  {
    return $this->payment_type === PaymentType::RECURRING;
  }

  public function isOnetime(): bool
  {
    return $this->payment_type === PaymentType::ONETIME;
  }
}
