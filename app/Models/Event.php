<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
  protected $table = 'events';
  protected $primaryKey = 'event_id';
  protected $fillable = [
    'payment_id',
    'event_name',
    'start_date',
    'end_date',
    'color',
    'bg_color'
  ];
  public function payment(): BelongsTo
  {
    return $this->belongsTo(Payment::class,'payment_id');
  }
}
