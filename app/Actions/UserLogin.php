<?php

namespace App\Actions;

use App\Http\Requests\UserLoginRequest;
use App\Http\Resources\UserResources;
use App\Traits\HasJsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserLogin
{
    use HasJsonResponse;
    /**
     * Create a new class instance.
     */
    public function loginUser(UserLoginRequest $request)
    {
        $loginUser = User::where('email', $request->email)->first();
        
        if(!$loginUser || !Hash::check($request->password, $loginUser->password))
        {
            return $this->jsonResponse(HTTP_VALIDATION_ERROR, "Invalid Details Supplied.");
        }
        if ($loginUser->suspended_at || $loginUser->banned_at) {
            //$fail('Sorry your account may have been either suspended or banned');
            abort(HTTP_BAD_REQUEST, 'Sorry your account may have been either suspended or banned.');
        }

            $token = $loginUser->createToken('userAuthToken')->plainTextToken;
            $loginUser->token = $token;
            $loginUser->save();

            $logArr['rate'] = $request->all();
            $logArr['ip']  =   $request->getClientIp();
            $logArr['user_agent'] = $request->userAgent();
            
            //To log activity
            activity()
                ->causedBy($loginUser) // The user who performed the action
                ->performedOn($loginUser) // The model that was acted upon
                ->withProperties($logArr) // Additional data you want to log
                ->log('Log in');

            return $this->jsonResponse(HTTP_CREATED, "Login Successful.", [new UserResources($loginUser)]);
    }
}
