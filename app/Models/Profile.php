<?php

namespace App\Models;

use App\Traits\ModelTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Tekkenking\Documan\DocumanCast;

class Profile extends Model
{
    use HasFactory, ModelTraits, Notifiable;

    protected $casts = [
        'dob'                       =>  'date',
        'transaction_limit_date'    =>  'date',
        'not_compliance_at'         =>  'datetime',
        'id_issue_date'             =>  'date',
        'id_expiry_date'            =>  'date',
        'shufti_response'           =>  'array',
        'kaasi_metas'               =>  'array',
        'idtype_metas'              => 'array',
        'currency_cloud_account'    => 'array',
        'face_verified_at'          => 'datetime',
        'face_verification_metas'   => 'array',
        'monoova_payid'             => 'array',

        'profile_photo' => DocumanCast::class . ':profile_photos:medium',
        'scanned_id_front' => DocumanCast::class . ':user_docs:medium',
        'scanned_id_rear' => DocumanCast::class . ':user_docs:medium',
    ];

    public function user()
    {
        return $this->morphOne(User::class, 'profile');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function userUploads(): HasMany
    {
        return $this->hasMany(UserUpload::class);
    }

    public function userUuploadsLastActive(): HasOne
    {
        return $this
            ->hasOne(UserUpload::class)
            ->where(function ($qr) {
                $qr->whereNotNull('expired_at')
                    ->where('expired_at', '>', today());
            });
    }

    public function fullname(): string
    {
        $name  = $this->firstname.' ';
        $name .= ($this->othernames) ? $this->othernames . ' ' : '';
        $name .= $this->lastname;
        return $name;
    }
}
