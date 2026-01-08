<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvoiceSetting extends Model
{
    protected $table = 'invoice_settings';
    protected $primaryKey = 'invoice_setting_id';
    protected $casts = [
        'extra' => 'array',
    ];
    protected $fillable = [
    'company_name',
    'company_email',
    'company_phone',
    'company_address',
    'logo',
    'footer',
    'extra'
  ];
  public function invoices(): HasMany
  {
    return $this->hasMany(Invoice::class, 'invoice_setting_id');
  }
   
  public function getLogoAttribute(): ?string
  {
    return !empty($this->attributes['logo'])
        ? asset('storage/' . $this->attributes['logo'])
        : null;

   }
}