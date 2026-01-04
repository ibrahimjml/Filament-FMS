<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payments';

    protected $primaryKey = 'payment_id';

    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'status' => PaymentStatus::class,
        'payment_amount' => 'decimal:2',
        'next_payment' => 'date',
    ];
   protected $fillable = [
       'income_id',
       'payment_amount',
       'description',
       'is_priority',
       'status',
       'next_payment',
       'paid_at',
       'discount_id',
   ];

    public function income(): BelongsTo
    {
        return $this->belongsTo(Income::class, 'income_id');
    }
    public function isPriority()
    {
      return $this->is_priority === 1;
    }
}
