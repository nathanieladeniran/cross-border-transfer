<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class ProfileResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "uuid" => $this->uuid,
            "country_id" => $this->country_id,
            "gender" => $this->gender ?? '',
            "transactions_count" => $this->transactions_counts,
            "kaasi_sender_accepted" => $this->kaasi_sender_accepted,
            "occupation" => $this->occupation ?? '',
            "occupation_industry" => $this->occupation_industry ?? '',
            "occupation_industry_id" => $this->cosmosec_occupation_industry ?? '',
            "estimated_monthly_send" => $this->estimated_monthly_send,
            "member_id" => $this->member_id,
            "firstname" => $this->firstname,
            "lastname" => $this->lastname,
            "othernames" => $this->othernames,
            "address_line_1" => $this->address_line_1,
            "address_line_2" => $this->address_line_2,
            "suburb" => $this->suburb ?? '',
            "postcode" => $this->postcode ?? '',
            "mobile_phone" => $this->mobile_phone ?? '',
            "card_issuer" => $this->card_issuer ?? '',
            "card_number" => $this->card_number ?? '',
            "idscan_path" => $this->scanned_id_front->medium ?? '',
            "back_idscan_path" => $this->scanned_id_rear->medium ?? '',
            "unit_no" => $this->unit_no ?? '',
            "street_name" => $this->street_name ?? '',
            "street_no" => $this->street_no ?? '',
            "kaasi_status" => $this->kaasi_status ?? '',
            "profile_photo" => $this->profile_photo->medium ?? '',
            "walletbalance" => $this->walletbalance,
            "local_limit_amount" => $this->local_limit_amount,
            "dob" => Carbon::parse($this->dob)->format('Y-d-m'),
            "id_issue_date" => Carbon::parse($this->id_issue_date)->format('Y-d-m'),
            "id_expiry_date" => Carbon::parse($this->id_expiry_date)->format('Y-d-m'),
            "id_number" => $this->idnumber,
            "black_listed" => $this->black_listed,
            "transaction_limit_date" => Carbon::parse($this->transaction_limit_date)->toDateString(),
            "personal_details_at" => $this->personal_details_at,
            "kyc_details_at" => $this->kyc_details_at,
            "address_details_at" => $this->address_details_at,
            "transaction_details_at" => ($this->estimated_monthly_send && $this->outbounds),
            "not_compliance_at" => $this->not_compliance_at ? 'yes' : 'no',
            "compliance_reason" => $this->compliance_reason,
            "shufti_profile_at" => $this->shufti_profile_at,
            "shuft_pro_response" => $this->shufti_response,
            "idtype_metas" => $this->idtype_metas,
            "idtype_id" =>  $this->idtype_id,
            "member_since"  =>  Carbon::parse($this->created_at)->format('Y-d-m'),
            "face_verified_at" => $this->face_verified_at,
            //"face_verification_mode" => get_face_verification_mode($this),
        ];
    }
}
