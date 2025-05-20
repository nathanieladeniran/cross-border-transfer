<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Beneficiary extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'kaasi_metas' => 'array'
    ];

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class, 'beneficiary_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fullname(): string
    {
        return $this->first_name .' '.$this->last_name;
    }
}
