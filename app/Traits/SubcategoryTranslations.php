<?php

namespace App\Traits;

use App\Models\SubcategoryTranslation;
use Illuminate\Support\Facades\App;

trait SubcategoryTranslations
{
    public function translations()
    {
        return $this->hasMany(SubcategoryTranslation::class, 'subcategory_id');
    }

    public function translation()
    {
        return $this->hasOne(SubcategoryTranslation::class, 'subcategory_id')->where('lang_code', App::getLocale());
    }

    public function getNameAttribute()
    {
        return optional($this->translation)->sub_name ?? $this->sub_name;
    }
}
