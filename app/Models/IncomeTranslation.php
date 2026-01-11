<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomeTranslation extends Model
{
    protected $table = 'income_translations';
    protected $fillable = ['income_id','lang_code', 'description'];
}
