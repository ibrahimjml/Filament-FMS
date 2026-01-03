<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientTypeRelation extends Model
{
  protected $table = 'client_types_relation';
  protected $primaryKey = 'id';
    protected $fillable = [
      'client_id', 'type_id', 'created_at',
  ];
}
