<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
  use SoftDeletes;
  protected $table = 'invoices';
  protected $primaryKey = 'invoice_id';
  protected $dates = ['deleted_at'];
  protected $casts = [
    'amount' => 'decimal:2',
    'payment_amount' => 'decimal:2',
    'status' => InvoiceStatus::class,
    'issue_date' => 'date',
    'due_date' => 'date'
  ];
  protected $fillable = [
    'invoice_number',
    'income_id',
    'payment_id',
    'client_id',
    'amount',
    'payment_amount',
    'description',
    'status',
    'issue_date',
    'due_date'
  ];
  public function client(): BelongsTo
  {
    return $this->belongsTo(Client::class, 'client_id','client_id');
  }
  public function income(): BelongsTo
  {
    return $this->belongsTo(Income::class, 'income_id');
  }
  public function payment(): BelongsTo
  {
    return $this->belongsTo(Payment::class, 'payment_id');
  }
  public function getRemainingAttribute(): float
{
    $discount = $this->income?->discount_amount ?? 0;
    $paid = $this->payment?->payment_amount ?? 0;
    return max(($this->amount - $discount) - $paid, 0);
}
}
