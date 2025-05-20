<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminConnectionModel extends Model
{
    use HasFactory;

        //conection to admin database
        protected $connection = 'admin_db';
}
