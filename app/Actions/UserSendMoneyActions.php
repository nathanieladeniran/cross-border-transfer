<?php

namespace App\Actions;

use App\Models\Payin;
use App\Models\Transaction;
use App\Traits\HasJsonResponse;
use CosmoSecurity\CosmoSec;

class UserSendMoneyActions
{
    use HasJsonResponse;
    /**
     * Create a new class instance.
     */
    
    public function recallPoli($request)
    {
        $transaction = Transaction::where('reference', $request->ref)->first();
        
        if (!$transaction) {
            return $this->jsonResponse(HTTP_BAD_REQUEST, 'Transaction not found');
        }
        $payin = Payin::where('inner_name', $transaction->payin)->first();

        $trnsct = $this->complete_transaction($payin, $transaction);

        $data = [
            'for_payin' => $trnsct,
            'transaction_details' => $transaction,
        ];
        return $data;

        $this->jsonResponse(HTTP_SUCCESS, 'Transaction recalled', $data);
    }

    public function complete_transaction($payin, $transaction, $is_wallet_transaction=false)
    {
        $payin_class = $payin->bootstrap_class;
        $initiate_transaction = (new $payin_class())->boot($transaction, $payin, $is_wallet_transaction);
        return $initiate_transaction;
    }

    public function calculateTransactionAmlScore($transaction, $request)
    {
        $cosmoSec = new CosmoSec();
        $data = [
            'weekly_transaction_volume' => get_user_week_transaction_volume(),
            'number_of_beneficiaries' => current_user()->beneficiaries->count(),
            'destination_country' => $transaction->to_country_id,
            'customer_occupation_industry' => current_user()->profile->cosmosec_occupation_industry,
            'purpose_of_transaction' => $request['purpose_of_transaction_id']
        ];
        try {
            $response = $cosmoSec->calculate_transaction_risk($data);

            if ($response['status'] == 'success') {
                $transaction->risk_score = $response['data']['score'];
                $transaction->risk_type = $response['data']['risk_type'];
                $transaction->risk_metas = $response['data'];
                $transaction->save();
            } else {
                $transaction->status = Transaction::cancelled;
                $transaction->payin_status = Transaction::cancelled;
                $transaction->status_at = $transaction->payin_status_at = now();
                $transaction->completed_at = now();
                $transaction->comment = "Risk assessment failed";
                $transaction->save();
                $this->jsonResponse(HTTP_BAD_REQUEST, 'Update Profile: '. $response['message']);
            }
            return $response;
        } catch (\Exception $e) {

            $transaction->risk_score = .5;
            $transaction->risk_type = 'HIGH_RISK';
            $transaction->risk_metas = null;
            $transaction->save();
        }
    }

    public function initiateTransaction($data, $account, $user)
    {
        $transact_with_id = ($data['transact_as'] == "business") ? $user->businessUserAccounts->first()->id : $user->profile_id;
        $transact_with_type = $data['transact_as'];
        $transaction = $user->transactions()->create([
            'account_id' => $account->id,
            'payin' => $data['default_payin']['inner_name'],
            'from_country_id' => $data['from_country']['id'],
            'to_country_id' => $data['to_country']['id'],
            'reference' => generate_transfer_reference(15),
            'send_amount' => $data['send_amount'],
            'received_amount' => $data['receipent_amount'],
            'rate' => $data['rate'],
            'commission' => $data['fee'],
            'comment' => $data['reason'],
            'source_of_fund' => $data['source_of_fund'],
            'walletvalue' => 0,
            'status_at' => now(),
            'meta' => [
                'beneficiary' => $account->beneficiary()
                    ->withTrashed()
                    ->first()
                    ->toArray(),
                'account' => $account->toArray(),
            ],
            'payin_status' => null,
            'payout_status' => null,
            'bound_direction'   =>  $data['bound_direction'],
            'transactionwith_id' => $transact_with_id,
            'transactionwith_type' => $transact_with_type
        ]);
        log_activity($transaction, 'Transaction post-initiated by customer');

        $this->calculateTransactionAmlScore($transaction, $data);

        $user->profile->save();

        //Initiate transaction
        $response = $this->complete_transaction($data['default_payin'], $transaction);

        return [$response, $transaction];
    }
}
