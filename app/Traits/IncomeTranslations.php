<?php

namespace App\Traits;


use App\Models\IncomeTranslation;
use Illuminate\Support\Facades\App;

trait IncomeTranslations
{
    public function translations()
    {
        return $this->hasMany(IncomeTranslation::class, 'income_id');
    }

    public function translation()
    {
        return $this->hasOne(IncomeTranslation::class, 'income_id')->where('lang_code', App::getLocale());
    }

    public function getTransDescriptionAttribute()
    {
        return optional($this->translation)->description ?? $this->description;
    }
}
