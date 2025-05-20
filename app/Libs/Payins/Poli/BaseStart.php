<?php

namespace Payins\Poli;

use App\Models\Country;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;
use Payins\BaseStart as PayinBaseStart;

class BaseStart extends PayinBaseStart
{
    /**
     * Summary of country
     * @var string
     */
    protected string $country;

    /**
     * Summary of currency
     * @var string
     */
    protected string $currency;

    public function calledPoli() {
        return new \Payins\Poli\BasePoli();
    }

    public function init($is_wallet_transaction) {
        if (!$is_wallet_transaction) {
            return $this->initiate_transaction();
        }
        return response_beam()->throwOops('Wallet currently does not support this payin type.');
    }

    public function initialState($transaction)
    {
        $transaction->payin = $this->payin->inner_name; //confirm inner_name
        $transaction->payin_id = $this->payin->id;

        $transaction->payin_status = Transaction::failed;
        $transaction->payin_status_at = now();
        $transaction->status = Transaction::failed;
        $transaction->status_at = now();
        $transaction->completed_at = now();
    }

    public function successState($transaction, $payload)
    {
        $transaction->payin_payload = $payload;
        $transaction->payin_status = Transaction::successful;
        $transaction->payin_status_at = now();
        $transaction->status = Transaction::pending;
        $transaction->completed_at = NULL;

        $transaction->user->profile->transactions_count += 1;
        $transaction->user->profile->save();

    }

    public function failedState($transaction, $payload)
    {
        $transaction->completed_at = now();
        $transaction->payin_payload = $payload;
        //$transaction->payin_status = Transaction::failed;
        $transaction->payin_status_at = now();
        //$transaction->status = Transaction::failed;
        $transaction->status_at = now();
    }

    public function initiate_transaction()
    {

        $this->initialState($this->transaction);
        $this->transaction->save();

        $poli = $this->calledPoli();

        $poli->country = $this->country;
        $poli->currency = $this->currency;

        $response = $poli->initiateTransaction($this->transaction->send_amount, $this->transaction->reference);

        if (isset($response['Success']) && $response['Success'] === true) {
            return [
                'plugin' => 'poli',
                'status' => 'success',
                'NavigateURL' => $response['NavigateURL'],
                'ErrorCode' => $response['ErrorCode'],
                'ErrorMessage' => $response['ErrorMessage'],
                'TransactionRefNo' => $response['TransactionRefNo'],
            ];
        }

        return [
            'plugin' => 'poli',
            'status' => 'failed',
            'NavigateURL' => $response['NavigateURL'] ?? null,
            'ErrorCode' => $response['ErrorCode'] ?? null,
            'ErrorMessage' => $response['ErrorMessage'] ?? null,
            'TransactionRefNo' => $response['TransactionRefNo'] ?? null,
        ];

    }

    public function getUser()
    {
        return user();
    }

    public function getUserLastTransaction()
    {
        return Transaction::where('user_id', $this->getUser()->id)
        ->latest('id')->first();
    }

    public function getTransactionFromCountry()
    {
        $trnx = $this->getUserLastTransaction();
        return $trnx->from_country;
    }

    protected function verifyPoliToken($tokenKey)
    {
        $userLastTransactionCountry = $this->getTransactionFromCountry();
        $initPoli = $this->calledPoli();
        $initPoli->country = $userLastTransactionCountry->iso3;
        $initPoli->currency = $userLastTransactionCountry->currency;

        return $initPoli->getTransaction($tokenKey);
    }

    public function checkoutSuccess()
    {
        $response = $this->verifyPoliToken($this->flashToken());
        $reference = $response['MerchantReference'];
        $finish = Transaction::where('reference', $reference)->first();
        $this->successState($finish, $response);
        $finish->save();

        $user = $finish->user;
        $this->sendMailNotification($finish, $user);

        $messageArr = [
            'title' =>  'Success',
            'body'  =>  "Transaction successful."
        ];
        return $this->responsePayload($finish, 'success', $messageArr);

        // return route('poli_responses.poli_checkout', [
        //     'reference'         => $reference,
        //     'sent_amount'       => $finish->from_country()->first()->iso3 . ' ' . $finish->sent_amount,
        //     'received_amount'   => $finish->to_country()->first()->iso3 . ' ' . $finish->received_amount,
        //     'status'            => 'success',
        //     'date_time'         => $finish->payin_status_at,
        //     'transfer_type'     => $finish->payin,
        // ]);
    }

    public function checkoutFailed($status = 'failed')
    {
        $response = $this->verifyPoliToken($this->flashToken());

        if (isset($response['MerchantReference'])) {
            $reference = $response['MerchantReference'];

            $finish = Transaction::where('reference', $reference)->first();
            $this->failedState($finish, $response);
            $finish->save();

            $user = $finish->user;
            $user->profile->transactions_count -= 1;
            $user->profile->save();
            $messageArr = [
                'title' =>  'Failed',
                'body'  =>  "<div style='display: flex;justify-content: center;'><div style='text-align: left;font-size: 13px;font-weight: bold;width: 80%;color: #ea1908'><ul><li>If you CAN see your bank account being charged for this attempt, please contact (<a href='mailto:office@cosmoremit.com.au'>office@cosmoremit.com.au</a>). You may be required to provide bank evidence of deduction.</li><li>If CAN NOT see your bank account being charged for this attempt, you can retry the payment attempt.</li></ul></div></div>"
            ];

            return $this->responsePayload($finish, $status, $messageArr);
        }

        return $this->errorPayload();
        
    }

    public function checkoutCancelled()
    {
        $response = $this->checkoutFailed('cancelled');
        if($response['status'] == 'error') {
            return $response;
        }

        $response['response_message'] = 'Transaction cancelled';
        $response['response_title'] = 'Cancelled';

        return $response;
    }

    private function flashToken()
    {
        return request()->token ?? request()->Token;
    }

    public function checkoutWebhook($country_code)
    {
        //Log::info('POLi received webhook: TIMESTAMP: '. now());
        $country = Country::where('iso3', $country_code)->first();

        $initPoli = $this->calledPoli();
        $initPoli->country = $country->iso3;
        $initPoli->currency = $country->currency;
        $token = $this->flashToken();

        $response = $initPoli->getTransaction($token);

        //Log::info('POLi webhook call response', $response);

        if (!$response || !isset($response['MerchantReference'])) {
            return response([
                'status' => 'failed',
                'message' => 'Nudge received and failed by token',
            ], 403);
        }

        $reference = $response['MerchantReference'];

        $finish = Transaction::where('reference', $reference)->first();
        $user = $finish->user;

        if (!$finish->payin_payload) {
            if ($response['TransactionStatusCode'] === "Completed") {
                $this->successState($finish, $response);
                $this->sendMailNotification($finish, $user);
            } else {
                $this->failedState($finish, $response);
                $user->profile->transactions_count -= 1;
                $user->profile->save();
            }

            $finish->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Nudge received and processed',
        ]);
    }

    private function responsePayload($finish, $status, $messageArr)
    {
        return [
            'reference' => $finish->reference ?? null,
            'sent_amount' => $finish 
                            ? $finish->from_country()->first()->iso3 . ' ' . $finish->sent_amount 
                            : null,
            'received_amount' => $finish 
                            ? $finish->to_country()->first()->iso3 . ' ' . $finish->received_amount 
                            : null,
            'status'    => $status,
            'date_time' => $finish ? $finish->payin_status_at : null,
            'transfer_type'     => $finish ? $finish->payin : null,
            'response_message'  =>  $messageArr['body'],
            'response_title'    =>  $messageArr['title']
        ];
    }

    private function errorPayload()
    {
        return [
            'status'    =>  'error',
            'message'   =>  'Something went wrong'
        ];
    }

    public function frontend_poli($response_msg, $response_title, $reference = null, $sent_amount = null, $received_amount = null, $status = null, $date_time = null, $transfer_type = null)
    {
        $data = [
            'reference' => $reference ?? null,
            'sent_amount' => $sent_amount ?? null,
            'received_amount' => $received_amount ?? null,
            'status' => $status ?? null,
            'date_time' => $date_time ?? null,
            'transfer_type' => $transfer_type ?? null,
            'response_message'  =>  $response_msg,
            'response_title'    =>  $response_title
        ];
        return response_beam()->status('success')->data(['data' => $data])->get();
    }
}
