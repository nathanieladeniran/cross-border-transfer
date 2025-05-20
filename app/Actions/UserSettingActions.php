<?php

namespace App\Actions;

use App\Http\Requests\UserEmailSettingRequest;
use App\Http\Requests\UserSettingRequest;
use App\Http\Resources\UserResources;
use App\Models\Deactivation;
use App\Notifications\VerifyEmailNotification;
use App\Notifications\VerifyPhoneNumberOtp;
use App\Traits\HasJsonResponse;
use Carbon\Carbon;

class UserSettingActions
{
    use HasJsonResponse;
    /**
     * Create a new class instance.
     */
    public function updatePassword(UserSettingRequest $request)
    {
        $user = current_user();
        $user->password = $request->new_password;
        $user->save();
        
        return $this->jsonResponse(HTTP_CREATED, "Password Updated.");
    }

    public function updateEmail(UserEmailSettingRequest $request)
    {
        $user = current_user();
        $user->temp_email = $user->email;
        $user->email = $request->email;
        $user->email_otp = random_int(100000, 999999);
        $user->email_otp_verified_at = null;
        $user->email_otp_expires_at = now()->addHours(2);
        $user->profile->shufti_profile_at = null;
        $user->profile->kyc_at = null;
        $user->save();

        if ($user->profile->kyc_details_at && $user->profile->address_details_at) {
            $user->profile->call_shuftipro_at = Carbon::now()->addMinutes(config('shufti_pro.shufti_retry_minute') ?? 5);
        }
        $user->profile->save();

        $user->notify(new VerifyEmailNotification());
        $user->email = $user->temp_email;
        $user->temp_email = $request->email;
        $user->save();

        $user->profile->not_compliance_at = now();
        $user->profile->compliance_reason = 'Edited email';
        $user->profile->save();

        return $this->jsonResponse(HTTP_CREATED, "Email update successful, verify otp to complete operation. Thank you for making corrections on your profile. Please wait for our compliance team's approval");

    }

    public function verifyEmailOtp($request)
    {
        $user = current_user();
        
        $user->email_otp_verified_at = now();
        $user->email = $user->temp_email;

        $user->save();
        return $this->jsonResponse(HTTP_SUCCESS, "Email changed successfully", [new UserResources($user)]);
    }

    public function updatePhone($request)
    {
        $user = current_user();
        $prepared_phone_number = remove_country_code($request->phone, $user->profile->country_id);

        //update phone number and otp
        $user->profile->temp_phone = $user->profile->mobile_phone;
        $user->profile->mobile_phone = $prepared_phone_number;
        $user->phone_otp = random_int(100000, 999999);
        $user->phone_otp_verified_at = null;
        $user->phone_otp_expires_at = now()->addHours(2);
        $user->profile->shufti_profile_at = null;
        $user->profile->kyc_at = null;
        $user->save();
        if ($user->profile->kyc_details_at && $user->profile->address_details_at) {
            $user->profile->call_shuftipro_at = Carbon::now()->addMinutes(config('shufti_pro.shufti_retry_minute') ?? 5);
        }
        $user->profile->save();

        //$user->notify(new VerifyPhoneNumberOtp());

        $user->profile->mobile_phone = $user->profile->temp_phone;
        $user->profile->temp_phone = $prepared_phone_number;
        $user->profile->not_compliance_at = now();
        $user->profile->compliance_reason = 'Edited phone number';
   
        $user->profile->save();

        return $this->jsonResponse(HTTP_CREATED, "Phone Otp resent. Thank you for making corrections on your profile. Please wait for our compliance team's approval");
       
    }

    public function verifyPhoneOtp($request)
    {
        $user = current_user();

        $user->phone_otp_verified_at = now();
        $user->profile->mobile_phone = $user->profile->temp_phone;
        $user->save();
        $user->profile->save();

        return $this->jsonResponse(HTTP_SUCCESS, "Phone changed successfully", [new UserResources($user)]);
    }

    public function setNotificationMethod($request)
    {
        $user = current_user();
        $user->user_setting()->update([
            'email' => $request->notification['email'],
            'sms' => $request->notification['sms']
        ]);

        return $this->jsonResponse(HTTP_SUCCESS, "Notification method updated!", [new UserResources($user)]);
    }

    public function setLanguage($request)
    {
        $user = current_user();
        $user->user_setting()->update(['language' => $request->language]);

        return $this->jsonResponse(HTTP_SUCCESS, "Language updated!", [new UserResources($user)]);
    }

    public function requestDeactivation($request, $reasons)
    {
        $user = current_user();

        // check if user is already deactivated
        if ($user->account_status == 'deactivated')
        {
            return $this->jsonResponse(HTTP_VALIDATION_ERROR, 'Account already deactivated');
        }

        $user->deactivation_request = now();
        $user->save();

        $user->deactivation()->create([
            'reasons' => $reasons,
            'comment' => $request->comment,
        ]);

        return $this->jsonResponse(HTTP_SUCCESS, "Deactivation request sent successfully", [new UserResources($user)]);
    }

    public function cancelDeactivationRequest()
    {
        $user = current_user();

        $deactivations = Deactivation::where('user_id', $user->id)->get();

        if (count($deactivations)>0) {
            foreach ($deactivations as $key => $deactivation) {
                $deactivation->delete();
            }
            $user->deactivation_request = null;
            $user->save();

            return $this->jsonResponse(HTTP_SUCCESS, "Deactivation request cancelled");
        }

        return $this->jsonResponse(HTTP_VALIDATION_ERROR, 'Request already cancelled, no deactivation reason found.');

    }

    public function device_history()
    {
        $user = current_user();
        //return $user->minion()->get();
                    // ->save();
    }


}
