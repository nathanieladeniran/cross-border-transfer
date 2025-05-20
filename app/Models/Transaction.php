<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends AdminConnectionModel
{
    use HasFactory, SoftDeletes;

    const failed = 'failed';
    const successful = 'successful';
    const cancelled = 'cancelled';
    const reject = 'reject';
    const refund = 'refund';
    const pending = 'pending';
    const inprogress = 'inprogress';
    const suspended = 'suspended';
    const expired = 'expired';

    protected $casts = [
        'completed_at' => 'datetime',
        'payin_payload' => 'array',
        'payout_payload' => 'array',
        'suspended_at' => 'datetime',
        'verify_bank_transfer' => 'datetime',
        'meta' => 'array',
        'refunded_at' => 'datetime',
        'rejected_at' => 'datetime',
        'risk_metas' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactionwith()
    {
        return $this->morphTo();
    }
    /**
     * @return BelongsTo
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function from_country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function to_country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function payintype(): BelongsTo
    {
        //return $this->payin();
        return $this->belongsTo(Payin::class);
    }

    public function payin(): BelongsTo
    {
        return $this->belongsTo(Payin::class);
    }
}
