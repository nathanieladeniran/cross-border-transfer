<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\HasJsonResponse;

class IsProfileCompleted
{
    use HasJsonResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = current_user();

        // Check id expiry and move to non-compliance
        if ($user->profile_type == 'profiles' && today() >= $user->profile->id_expiry_date) {
            $user->profile->not_compliance_at = now();
            $user->profile->compliance_reason = "ID expired";
            $user->profile->save();
            return $this->jsonResponse(HTTP_SUCCESS, "Password Updated");
        }

        if (! $user->profile->cosmosec_occupation_industry) {

            return $this->jsonResponse(HTTP_BAD_REQUEST, "Update: Attach occupation industry to profile");
        }

        if (!$user->profile->address_details_at)
        {
            return $this->jsonResponse(HTTP_BAD_REQUEST, "Address details incomplete");
        }

        if (!$user->profile->personal_details_at)
        {
            return $this->jsonResponse(HTTP_BAD_REQUEST, "Personal details incomplete");
        }

        if (!$user->profile->kyc_details_at)
        {
            return $this->jsonResponse(HTTP_BAD_REQUEST, "KYC details incomplete");
        }

        if (!($user->email_otp_verified_at || $user->phone_otp_verified_at)) {

            return $this->jsonResponse(HTTP_BAD_REQUEST, "You must verify your email and phone before login in");
        }

        if ($user->suspended_at || $user->banned_at) {

            return $this->jsonResponse(HTTP_BAD_REQUEST, "Sorry your account may have been either suspended or banned");
        }

        return $next($request);
    }
}
