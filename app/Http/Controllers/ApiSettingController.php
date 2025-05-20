<?php

namespace App\Http\Controllers;

use App\Actions\UserSettingActions;
use App\Http\Requests\UserEmailSettingRequest;
use App\Http\Requests\UserPhoneSettingRequest;
use App\Http\Requests\UserSettingRequest;
use App\Models\DeactivateReason;
use Illuminate\Http\Request;

class ApiSettingController extends Controller
{
    //Update password
    public function updatePassword(UserSettingRequest $request)
    {
        $updatePassword = (new UserSettingActions())->updatePassword($request);
        return $updatePassword;
    }

    public function updateEmail(UserEmailSettingRequest $request)
    {
        $updateEmail = (new UserSettingActions())->updateEmail($request);
        return $updateEmail;
    }

    public function verifyEmailOtp(Request $request)
    {
        $user = current_user();
        $request->validate([
            'email_otp' => ['required', function($attribute, $value, $fail) use ($user){
                if ($user->email_otp_verified_at) {
                    $fail('Email is already verified');
                }
                //check if otp is expired
                if ($user->email_otp_expires_at < now()) {
                    $fail('Email Otp has expired');
                }
                //check if otp match
                if (! ($value == $user->email_otp)) {
                    $fail('Invalid Otp');
                }
            }]
        ]);
        $verifyOtp = (new UserSettingActions())->verifyEmailOtp($request);
        return $verifyOtp;
    }

    public function updatePhone(UserPhoneSettingRequest $request)
    {
        $updateEmail = (new UserSettingActions())->updatePhone($request);
        return $updateEmail;
    }

    public function verifyPhoneOtp(Request $request)
    {
        $user = current_user();
        $request->validate([
            'phone_otp' => ['required', function($attribute, $value, $fail) use ($user){
                // check if user exists
                if ($user->phone_otp_verified_at) {
                    $fail('Phone is already verified');
                }
                //check if otp is expired
                if ($user->phone_otp_expires_at < now()) {
                    $fail('Phone Otp has expired');
                }
                //check if otp match
                if (! ($value == $user->phone_otp)) {
                    $fail('Invalid Otp');
                }
            }]
        ]);

        $verifyOtp = (new UserSettingActions())->verifyPhoneOtp($request);
        return $verifyOtp;
    }

    public function updateNotificationMethod(Request $request)
    {
        $updateNotification = (new UserSettingActions())->setNotificationMethod($request);
        return $updateNotification;
    }

    public function updateLanguage(Request $request)
    {
        $updateLanguage = (new UserSettingActions())->setLanguage($request);
        return $updateLanguage;
    }

    public function requestDeactivation(Request $request)
    {
        $reasons = [];
        $request->validate([
            'reason_id' => ['required', function ($attribute, $value, $fail) use(&$reasons){
                foreach ($value as $val) {
                    $deactivation_reason = DeactivateReason::find($val);
                    if (! ($deactivation_reason)) {
                        return $fail('Invalid deactivation reason ID');
                    }
                    $reasons[$deactivation_reason->id] = $deactivation_reason->reason;
                }
            }]
        ]);
        $deactivate = (new UserSettingActions())->requestDeactivation($request, $reasons);
        return $deactivate;
    }

    public function cancelDeativationRequest(Request $request)
    {
        $cancelDeactivate = (new UserSettingActions())->cancelDeactivationRequest($request);
        return $cancelDeactivate;
    }

}
