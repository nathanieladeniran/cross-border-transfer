<?php

namespace App\Actions;

use App\Http\Requests\UserCreateRequest;
use App\Models\Country;
use App\Models\PasswordResetToken;
use App\Models\User;
use App\Models\Profile;
use App\Models\Temp;
use App\Notifications\OtpNotification;
use App\Notifications\PasswordChanged;
use App\Notifications\ResetPasswordLink;
use App\Notifications\VerificationSuccesfull;
use App\Traits\HasJsonResponse;
use App\Traits\ModelTraits;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CreateNewAccount
{
    use HasJsonResponse, ModelTraits, Notifiable;

    //Create a new User Account
    public function createNewUser(UserCreateRequest $request)
    {
        $newUser = User::where('email', $request->email)->first();

        abort_if($newUser, HTTP_BAD_REQUEST, "An account with this email already exist");

        $referrer = $request->has('ref') ? $request->query('ref') : $request->referrer;
        
        //save temporarily before verifying the OTP
        $emailOtp = random_int(100000, 999999);
        $phoneOtp = random_int(100000, 999999);

        $saveData = Temp::updateOrCreate(
            ['email' => $request->email],
            [
                'referrer' => $referrer ? $referrer : null,
                'referral_token' => random_int(10000000, 99999999),
                'email' => $request->email,
                'email_otp' => $emailOtp,
                'email_otp_expires_at' => Carbon::now()->addHours(2),
                'mobile_phone' => $request->mobile_phone,
                'country_id' => $request->country_id,
                'phone_otp' => $phoneOtp,
                'phone_otp_expires_at' => Carbon::now()->addHours(2),
                'policy_agreement' => $request->policy_agreement,
                'password' => Hash::make($request->password)
            ]
        );

        if ($saveData) {
            $saveData->notify(new OtpNotification($emailOtp));
            return $this->jsonResponse(HTTP_CREATED, "Data Saved, and OTP send to the registered email for account validation", ['uuid' => $saveData->uuid]);
        }
    }

    //verify the new OTP
    public function verifyOtp(Request $request)
    {
        // Validate the request data
        $request->validate([
            'otp' => 'required',
        ]);

        // Retrieve the OTP record
        $otpData = Temp::where('email', $request->email)->first();
        if (!$otpData)
            return $this->jsonResponse(HTTP_BAD_REQUEST, "Record no found.");

        if ($request->otp != $otpData->email_otp)
            return $this->jsonResponse(HTTP_BAD_REQUEST, "Invalid OTP.");

        if (now()->greaterThan($otpData->email_otp_expires_at))
            return $this->jsonResponse(HTTP_BAD_REQUEST, "OTP has expired.");

        $referrer = User::where('referral_token', $otpData->referral_token)->first();
        $country = Country::find($otpData->country_id);

        //creating the profile
        $profile = Profile::create([
            'member_id' => mt_rand(10000000, 99999999),
            'mobile_phone' => remove_country_code($otpData->mobile_phone, $country->phonecode),
            'country_id' => $otpData->country_id,
            'compliance_reason' => 'new profile',
            'not_compliance_at' => Carbon::now()
        ]);

        $password = $otpData->password;

        //Create User data on user table in relation to profile
        $user = $profile->user()->create([
            'uuid' => $profile->uuid,
            'email' => strtolower($otpData->email),
            'password' => $password,
            'email_verified_at' => Carbon::now(),
            'referrer' => $referrer ? $referrer->referral : null,
            'referrer_token' => $otpData->referral_token,
        ]);

        $user->user_setting()->create([
            'email' => true,
            'sms' => true,
        ]);

        $user->refresh();
        $user->notify(new VerificationSuccesfull());

        $otpData->delete();
        return $this->jsonResponse(HTTP_CREATED, "Account created succesfully.");
    }

    //Resend OTP
    public function sendNewOtp(Request $request)
    {
        // Find the user by email
        $user = Temp::where('email', $request->email)->first();

        if (!$user) {
            return $this->jsonResponse(HTTP_BAD_REQUEST, "Record not found.");
        }

        // Generate a new OTP
        $otp = random_int(100000, 999999);
        $user->email_otp = $otp;
        $user->email_otp_expires_at = Carbon::now()->addHours(2);
        $user->save();

        $user->notify(new OtpNotification($otp));
        return $this->jsonResponse(HTTP_CREATED, "OTP resent to the registered email succesfully.");
    }

    //Send passsword reset link
    public function resetLink(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        abort_if(!$user, HTTP_VALIDATION_ERROR, 'Failed to send password link');

        //check if the email exist in the password reset table
        $check_record = PasswordResetToken::where('email', $request->email);
        if ($check_record->exists()) {
            $check_record->delete();
        }
        $token = Str::random(64);

        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

        $user->notify(new ResetPasswordLink($token, $request->email));
        return $this->jsonResponse(HTTP_CREATED, "A reset link has been sent to the registered email succesfully.");
    }

    public function resetPassword(string $email, string $token, string $new_password)
    {
        try {
            //get user who request for a password reset from the password reset table
            $getUserRequest = PasswordResetToken::where([
                ['email', '=', $email],
                ['token', '=', $token],
            ])->first();

            if (!$getUserRequest) {
                return $this->jsonResponse(HTTP_BAD_REQUEST, "Your record cannot be found, therefore password reste failed.");
            }

            $user = User::where('email', $email)->first();

            if ($user) {
                $user->password = Hash::make($new_password);
                $user->save();
            }

            $user->notify(new PasswordChanged());
            return $this->jsonResponse(HTTP_CREATED, "Your password has been reset succesfully.");
        } catch (\Exception $e) {
            return $this->jsonResponse(HTTP_BAD_REQUEST, "Failed to reset password.");
        }
    }
}
