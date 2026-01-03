<?php

namespace App\Traits;

use App\Models\ClientTranslation;
use Illuminate\Support\Facades\App;

trait ClientTranslations
{
    public function translations()
    {
        return $this->hasMany(ClientTranslation::class, 'client_id');
    }

    public function translation()
    {
        return $this->hasOne(ClientTranslation::class, 'client_id')->where('lang_code', App::getLocale());
    }

    public function getFNameAttribute()
    {
        return optional($this->translation)->client_fname ?? $this->client_fname;
    }

    public function getLNameAttribute()
    {
        return optional($this->translation)->client_lname ?? $this->client_lname;
    }

    public function getFullNameAttribute()
    {
        if ($this->translation) {
            return $this->translation->client_fname.' '.$this->translation->client_lname;
        }

        return $this->client_fname.' '.$this->client_lname;
    }
}
