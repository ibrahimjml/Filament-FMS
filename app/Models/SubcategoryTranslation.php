<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubcategoryTranslation extends Model
{
    protected $table = 'subcategories_translations';
    protected $fillable = ['subcategory_id','lang_code', 'sub_name'];
}
