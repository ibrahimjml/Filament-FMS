<?php

namespace App\Models;

use App\Traits\ClientTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
  use HasFactory, SoftDeletes, ClientTranslations;
  protected $table = "clients";
  protected $primaryKey = 'client_id';
  protected $dates = [
    'deleted_at',
    'created_at',
    'updated_at',
  ];
  protected $fillable = ['client_fname', 'client_lname', 'client_phone', 'email'];
public function types()
  {
      return $this->belongsToMany(ClientType::class, 'client_types_relation', 'client_id', 'type_id');

  }
  public function income(): HasMany
  {
    return $this->hasMany(Income::class, 'client_id');
  }
}
