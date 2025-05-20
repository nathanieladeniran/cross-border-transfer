<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Payin extends AdminConnectionModel
{
    use HasFactory;

    public function countries() : BelongsToMany
    {
        return $this->belongsToMany(Country::class);
    }
}
