<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PopMessage extends Model
{
    use HasFactory, SoftDeletes;

    const sticky = 'sticky';
    const pop = 'pop';

    protected $casts = ['expired_at' => 'datetime'];

    // public function readPopMessage(): HasMany
    // {
    //     return $this->hasMany(ReadPopMessage::class);
    // }

    // public function scopeActive($query)
    // {
    //     return $query->where('expired_at', '>', now())
    //         ->where('active', 'yes');
    // }
}
