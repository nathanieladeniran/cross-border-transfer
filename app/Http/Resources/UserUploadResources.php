<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserUploadResources extends JsonResource
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
            'sender' => $this->profile ? $this->profile->firstname . ' ' . $this->profile->lastname : '',
            'member_id' => $this->profile ? $this->profile->member_id : '',
            'title' => $this->title,
            'status' => $this->status,
            'message' => $this->message,
            'comment' => $this->comment,
            'files' => UserFileUploadResources::collection($this->userFilesUploads()->get()),
            'created_at' => Carbon::parse($this->created_at)->toDateString(),
            'valid_till' => Carbon::parse($this->expired_at)->toDateString()
        ];
    }
}
