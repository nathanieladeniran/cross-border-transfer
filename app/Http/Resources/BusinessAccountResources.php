<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessAccountResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'uuid' => $this->uuid,
            'user_type' => $this->user_type,
            'business_reference_id' => $this->business_reference_id,
            'business_name' => $this->business_name,
            'business_location' => $this->business_location,
            'business_type' => $this->business_type,
            'business_category' => $this->business_category,
            'business_verify_status' => $this->business_verify_status,
            'business_account_status' => $this->business_name_status,
            'certificate_image' => $this->certificate_image ? pathinfo($this->certificate_image->medium) : '', 
            'incorporation_date' => $this->incorporation_date,
            'registration_number' => $this->registration_number,
            'jurisdiction_code' => $this->jurisdiction_code,
            'banned_at' => $this->banned_at,
            'suspended_at' => $this->suspended_at,
            'deactivated' => $this->deativated,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
