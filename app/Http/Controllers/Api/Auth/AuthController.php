<?php

namespace App\Http\Controllers\Api\Auth;

use App\Actions\CreateNewAccount;
use App\Actions\UserLogin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserLoginRequest;

class AuthController extends Controller
{
    /**Add new User */
    public function addNewUser(UserCreateRequest $request)
    {
        $newUser = (new CreateNewAccount())->createNewUser($request);
        return $newUser;
    }

    /**Verify User With OTP */
    public function verifyEmailWithOtp(Request $request)
    {
        $newUser = (new CreateNewAccount())->verifyOtp($request);
        return $newUser;
    }

    public function signIn(UserLoginRequest $request)
    {
        $user = (new UserLogin())->loginUser($request);
        return $user;
    }

    public function resendOtp(Request $request)
    {
        $sendOtp = (new CreateNewAccount())->sendNewOtp($request);
        return $sendOtp;
    }

    public function logout(Request $request)
    {
        if ($request->user()) {
            // Revoke the token that was used to authenticate the current request
            $request->user()->currentAccessToken()->delete();

            // Return a JSON response indicating successful logout
            return $this->jsonResponse(HTTP_CREATED, "Successfully logged out");
        }
    }

    public function sendResetLink(Request $request)
    {
        $sendLink = (new CreateNewAccount())->resetLink($request);
        return $sendLink;
    }

    public function completePasswordReset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'new_password' => 'required|string|min:6',
            'token' => 'required'
        ]);
        $resetPassword = (new CreateNewAccount())->resetPassword($request->email, $request->token, $request->new_password);
        return $resetPassword;
    }
}
