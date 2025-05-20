<?php

namespace App\Models;

use App\Traits\ModelTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Tekkenking\Documan\DocumanCast;

class BusinessUserAccount extends Model
{
    use HasFactory, ModelTraits, SoftDeletes, Notifiable;

    const VERIFIED = 'verified';
    const PENDING = 'pending';
    const FAILED = 'failed';
    const PROCESSING = 'processing';

    protected $casts = [
        'incorporation_date'     =>  'date',
        'certificate_image' => DocumanCast::class . ':certificate_image:medium',
        'meta' => 'array'
    ];

    public function user(): MorphTo
    {
        return $this->morphTo();
    }
}
