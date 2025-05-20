<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserUpload extends GuardedModel
{
    use HasFactory, SoftDeletes;
    
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function userFilesUploads()
    {
        return $this->hasMany(UserFileUpload::class, 'user_upload_id');
    }
}
