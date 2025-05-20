<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'token' => $this->token,
            "email" => $this->email,
            'risk_score' => $this->risk_score,
            'risk_type' => $this->risk_type,
            "email_otp_expires_at" => $this->email_otp_expires_at,
            "email_otp_verified_at" => $this->email_otp_verified_at,
            "phone_otp_expires_at" => $this->phone_otp_expires_at,
            "phone_otp_verified_at" => $this->phone_otp_verified_at,
            "confirmed_at" => $this->confirmed_at,
            "profile" => new ProfileResources($this->profile),
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "suspended_at" => $this->suspended_at,
            "banned_at" => $this->banned_at,
            "account_status" => $this->account_status,
        ];
    }
}
