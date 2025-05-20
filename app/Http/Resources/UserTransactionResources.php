<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserTransactionResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $status = $this->status;
        if($status === 'suspended') {
            $status = 'pending';
        }

        return [
            "uuid" => $this->uuid,
            "from_country_id" => $this->from_country_id,
            "from_country" => $this->from_country,
            "to_country_id" => $this->to_country_id,
            "to_country" => $this->to_country,
            "account_id" => $this->account_id,
            "to_country_currency" => $this->to_country_currency,
            "payin" => $this->payintype->display_name ?? null,
            "reference" => $this->reference,
            "payin_reference" => $this->payin_reference,
            "send_amount" => $this->send_amount,
            "received_amount" => $this->received_amount,
            "source_of_fund" => $this->source_of_fund,
            "comment" => $this->comment,
            "rate" => $this->rate,
            "meta" => $this->meta,
            "payin_payload" => $this->payin_payload,
            "status" => $status,
            "payin_status" => $this->payin_status,
            "payin_status_at" => $this->payin_status_at,
            "status_at" => $this->status_at,
            "verify_bank_transfer" => $this->verify_bank_transfer,
            "completed_at" => $this->completed_at,
            "created_at" => date('Y-m-d', strtotime($this->created_at)),
            "bound_direction"   =>  $this->bound_direction,
            "risk_score" => $this->risk_score,
            "risk_type" => $this->risk_type,
            "transctionwith_id" => $this->transactionwith_id,
            "transctionwith_type" => $this->transactionwith_type

        ];
    }
}
