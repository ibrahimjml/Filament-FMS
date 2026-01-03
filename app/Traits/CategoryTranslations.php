<?php

namespace App\Traits;

use App\Models\CategoryTranslation;
use Illuminate\Support\Facades\App;

trait CategoryTranslations
{
    public function translations()
    {
        return $this->hasMany(CategoryTranslation::class, 'category_id');
    }

    public function translation()
    {
        return $this->hasOne(CategoryTranslation::class, 'category_id')->where('lang_code', App::getLocale());
    }

    public function getNameAttribute()
    {
        return optional($this->translation)->category_name ?? $this->category_name;
    }
}
