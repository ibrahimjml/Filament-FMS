<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Outcome extends Model
{
  use HasFactory, SoftDeletes;
  protected $table = 'outcomes';
  protected $primaryKey = 'outcome_id';
  protected $dates = ['deleted_at'];
  protected $with = [
    'subcategory.category'
  ];
   protected $fillable = [
      'subcategory_id', 'amount', 'description', 'date', 'is_deleted'
  ];

  public function subcategory(): BelongsTo
  {
    return $this->belongsTo(Subcategory::class, 'subcategory_id');
  }
public function getCategoryAttribute()
{
    return $this->subcategory?->category;
}
}
