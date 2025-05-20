<?php

use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

if (! function_exists('current_user')) {
    //set logged in user globaly
    function current_user()
    {
        return request()->user();
    }
}

if (! function_exists('log_activity')) {
    function log_activity($data, $msg=null, $whatWasChanged=null)
    {
        $logArr['ip']  =   request()->getClientIp();
        $logArr['user_agent'] = request()->userAgent();
        $logArr['request'] = ($whatWasChanged) ?: request()->all();
        //To log activity
        activity()
            ->causedBy(current_user()) // The user who performed the action
            ->performedOn($data) // The model that was acted upon
            ->withProperties($logArr) // Additional data you want to log
            ->log($msg);

    }
}

if (!function_exists('remove_country_code')) {
    function remove_country_code($phone, $countrycode)
    {

        if (Str::startsWith($phone, '00')) {
            //Removing the first zero in the phone number
            return '0'. Str::replaceFirst('00', '', Str::replaceFirst($countrycode, '', $phone));
        }

        //Is leading zero?
        if (Str::startsWith($phone, '0')) {
            //Removing the first zero in the phone number
            return $phone;
        }

        //Does the number start with +
        if (Str::startsWith($phone, '+')) {
            //Removing the first zero in the phone number
            return '0'. Str::replaceFirst('+', '', Str::replaceFirst($countrycode, '', $phone));
        }

        return '0'.$phone;

    }

    if(! function_exists('reset_user_transaction_limit_to_default')) {
        function reset_user_transaction_limit_to_default($user, $country_limit)
        {
            if($user && $country_limit) {
    
                $isIdUploadedStillValid = $user->profile->userUuploadsLastActive()->latest()->first();
    
                if(!$isIdUploadedStillValid && $user->profile->local_limit_amount > 0) {
    
                    $user->profile()->update([
                        'transaction_limit_date' => now()->addDays($country_limit->days),
                        'local_limit_amount' => 0,
                        'transaction_days' => 0,
                        'transaction_times' => 0,
                    ]);
    
                    $user->refresh();
    
                }
    
            }
    
        }
    }

    if (!function_exists('unformat_money')) {
        function unformat_money($money = 0.00)
        {
            $money = ($money) ? $money : 0;
            $money = format_num(extract_char((string) $money, array('unformat_money')), 2, '.', '');
            return floatval($money) + 0;
        }
    }

    if (!function_exists('query_user_transactions')) {
        function query_user_transactions($user)
        {
            /**
             * Sums user transactions between date ranges
             */
    
            $user = $user ?? current_user();
            $profile = $user->profile;
            $countryLimit = $profile->country->countrylimit;
    
            if ($countryLimit) {
                $limit_end_date = $profile->transaction_limit_date;
    
                $userTransactionDays = ($profile->transaction_days > 0)
                ? $profile->transaction_days
                : $countryLimit['days'];
    
                if (!$limit_end_date) {
                    $limit_end_date = now()->addDays($userTransactionDays);
                    $profile->transaction_limit_date = $limit_end_date;
                    $profile->save();
                }
    
                //check if user still has eligible limit
                $start_date = $limit_end_date
                    ->subDays($userTransactionDays)
                    ->startOfDay()
                    ->toDateTimeString();
    
                // Check User Transactions with the limit_end_date
                $end_date = $profile->transaction_limit_date
                    ->endOfDay()
                    ->toDateTimeString();
    
                $data = $user->transactions()
                    ->where(function($qr) {
                        $qr->where('status', 'pending')
                        ->orWhere('status', 'successful')
                        ->orWhere('status', 'suspended');
                    })->whereBetween('created_at', [$start_date, $end_date]);
                return $data;
            }
    
            return null;
        }
    }
    

    if (!function_exists('sum_user_transactions')) {
        function sum_user_transactions($user = null)
        {
            $result = query_user_transactions($user);
    
            if ($result) {
                return $result->sum('send_amount');
            }
    
            return 0;
        }
    }

    if (!function_exists('format_num')) {
        function format_num($num, $dec_places = 2, $dec_symbol = '.', $thousand_group = ','): string
        {
            return number_format((float) $num, $dec_places, $dec_symbol, $thousand_group);
        }
    }

    if (!function_exists('extract_char')) {
        //Please do not parse in float and INT as you'd get unexpected result
        function extract_char($string = '', array $type = null, $rep = '', $single = false)
        {
            if ($string == '') {
                trigger_error(__FUNCTION__ . ' Requires 1 string parameter', E_USER_WARNING);
            }
    
            $allowedTypes = array('float' => '([\d]+\.[\d]+)|',
                'int' => '([\d]+)|',
                'text' => '([a-zA-Z \s]+)|',
                'symbol' => '([^\s\t0-9a-zA-Z])|',
                'symbol2' => '([\_\-\@])',
                'html_tag' => '(<[^<>]+>)|',
                'unformat_money' => '([0-9\.-]+)',
            );
            $allowedkey = $type == null ? array('text') : $type;
    
            $types = '';
            foreach ($allowedkey as $key) {
                $key = strtolower($key);
                if (isset($allowedTypes[$key])) {
                    $types .= $allowedTypes[$key];
                } else {
                    trigger_error($type . ': [' . $key . '] is not allowed. text is assumed', E_USER_NOTICE);
                    $types .= $allowedTypes['text'];
                }
            }
    
            if ($rep != '') {
                $result = preg_replace("/$types/", $rep, $string);
            } else {
                if ($single == true) {
                    preg_match("/$types/", $string, $match);
                } else {
                    preg_match_all("/$types/", $string, $match);
                }
    
                $result = implode('', $match[0]);
            }
    
            return $result;
    
        }
    }

    if (!function_exists('getUserTransaction')) {

        function getUserTransactions($user = null)
        {
            $user = $user ?? current_user();
            $country_limit = $user->profile->country->countrylimit;
            if (! $country_limit) {
                return [];
            }
            $currency = $country_limit->country->currency;
    
            $workingLimitAmount = unformat_money(($user->profile->local_limit_amount > 0)
                ? $user->profile->local_limit_amount
                : $country_limit->amount);
    
            $total_sent_amount = unformat_money(sum_user_transactions($user));
            $remaining_amount_to_send = $workingLimitAmount - $total_sent_amount;
    
            return ['currency' => $currency, 'remaining_amount' => $remaining_amount_to_send];
        }
    }

    if (!function_exists('generate_transfer_reference')) {
        function generate_transfer_reference($len = 12)
        {
            return config('app.transfer_reference_prefix') . generate_token($len, [new Transaction(), 'reference'], true);
        }
    }

    if (! function_exists('get_user_week_transaction_volume')) {
        function get_user_week_transaction_volume()
        {
            $transactions = Transaction::where('user_id', current_user()->id)
                                        ->where('created_at', '>=', Carbon::now()->subWeek())
                                        ->count();
            return $transactions;
        }
    }

}