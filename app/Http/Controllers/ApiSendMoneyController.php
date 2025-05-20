<?php

namespace App\Http\Controllers;

use App\Actions\UserSendMoneyActions;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Http\Request;

class ApiSendMoneyController extends Controller
{
    public function recallPoli(Request $request)
    {
        $recall = (new UserSendMoneyActions())->recallPoli($request);
        return $recall;
    }
    public function initiateTransaction(ClientRequest $request, $data, $account, $user)
    {
        $user = current_user();

        $request->validate([
            'wallet_uuid' => ['nullable','exists:user_wallets,uuid'],
            'account_id' => ['required', 'numeric'],
            'payin_id' => ['required_if:wallet_uuid,null'],
            'from_country_id' => ['required', 'numeric'],
            'to_country_id' => ['required', 'numeric'],
            'send_amount' => ['required'],
            'receipient_amount' => ['required'],
            'rate' => ['required', 'numeric'],
            'fee' => ['required', 'numeric'],
            'reason' => ['required', 'string'],
            'purpose_of_transaction_id' => ['nullable', 'integer'],
            'source_of_fund' => ['required', 'string'],
            'transact_as' => ['required', 'in:profiles,business']
        ], [
            'wallet_uuid.exists' => 'Wallet not found.'
        ]);

        if ($user->profile->cosmosec_occupation_industry == null) {
            return response_beam()->throwOops('Update Profile: Please update your occupation industry to continue.', 422);
        }

        // Register payId if not existing
        if (!$user->userPayId || !$user->userPayId->payid) {
            $payId = (new MonoovaPayId())->registerPayId($user);
            if (!$payId->payid) {
                return response_beam()->throwOops('Unable to complete your transaction. Please try again or contact support.', 422);
            }
        }
        //$sendMoney = (new UserSendMoneyActions())->initiateTransaction($data, $account, $user);
    }
}
