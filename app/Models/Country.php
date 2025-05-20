<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Country extends Model
{
    use HasFactory;

    public function countryLimit(): HasOne
    {
        return $this->hasOne(CountryLimit::class);
    }

    public function payins()
    {
        return $this->belongsToMany(Payin::class);
    }
}
