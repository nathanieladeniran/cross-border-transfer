<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tekkenking\Documan\DocumanCast;

class UserFileUpload extends GuardedModel
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'file_path' =>  DocumanCast::class . ':user_docs:medium',
    ];

    public function filesUploads(): BelongsTo
    {
        return $this->belongsTo(UserUpload::class, 'user_upload_id');
    }
}
