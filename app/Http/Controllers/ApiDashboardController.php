<?php

namespace App\Http\Controllers;

use App\Models\CountryLimit;
use Illuminate\Http\Request;
use App\Models\PopMessage as Pop;
use App\Models\Rate;

class ApiDashboardController extends Controller
{
    public function meta(CountryLimit $countryLimit)
    {
        $user = current_user();

        $countryUserLimit = 0;
        $countryDaysLimit = 0;
        $countryYearlyLimit = 0;
        $countryTimesLimit = 0;

        $countryLimit = $countryLimit::where('country_id', $user->profile->country_id)->first();

        reset_user_transaction_limit_to_default($user, $countryLimit);

        $profile = $user->profile;

        if ($countryLimit) {
            $countryUserLimit = ($profile->local_limit_amount > 0)
                ? $profile->local_limit_amount
                : $countryLimit->amount;

            $countryDaysLimit = ($profile->transaction_days > 0)
                ? $profile->transaction_days
                : $countryLimit->days;

            $countryYearlyLimit = $countryLimit->yearly_amount;

            $countryTimesLimit = ($profile->transaction_times > 0)
                ? $profile->transaction_times
                : $countryLimit->times;
        }

        $limitAmount = getUserTransactions($user);
        if (!$limitAmount) {
            return $this->jsonResponse(HTTP_NOT_FOUND, "A problem occured or country limit not found");
        }

        $sent = sum_user_transactions($user);

        $stickys = Pop::where('expired_at', '>=', today())
            ->where('purpose', Pop::sticky)
            ->where('active', 'yes')
            ->select('id', 'message', 'title', 'type')
            ->get();

        return $this->jsonResponse(HTTP_SUCCESS, "Dashboard Meta", [
            "country_limit" => [
                "user_limit"    =>  $countryUserLimit,
                "yearly_limit"  =>  $countryYearlyLimit,
                "limit_days"    =>  $countryDaysLimit,
                "limit_times"   =>  $countryTimesLimit
            ],
            'limit_amount' => $limitAmount,
            'total_sent' => $sent,
            'sticky_messages' => $stickys,
        ]);
    }

    public function appDashboardRate($from_country_id, Rate $rate)
    {
        $rates = $rate->where('from_country_id', $from_country_id)->with(['to_country' => function($qr) {
            $qr->select('id', 'name', 'iso2', 'iso3', 'currency');
        }])
            ->select('id', 'from_country_id', 'to_country_id', 'buy', 'promorate')
            ->get();
            
        return $this->jsonResponse(HTTP_SUCCESS, "rate for", [ 'rates' =>  $rates] );
    }
}
