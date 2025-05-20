<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuardedModel extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
}
