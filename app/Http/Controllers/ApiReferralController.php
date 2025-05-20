<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiReferralController extends Controller
{
    public function referrals()
    {
        $user = current_user();
        return $user->referrals()->get();
    }
}
