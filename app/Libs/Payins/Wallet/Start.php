<?php

namespace Payins\Wallet;

use App\Models\Transaction;
use Illuminate\Support\Facades\Log;
use Payins\BaseStart;

class Start extends BaseStart
{
    public function init($is_wallet_transaction)
    {
        return $this->initiate_transaction();
    }

    public function initiate_transaction()
    {
        $this->transaction->payin = $this->payin->inner_name; //confirm inner_name
        $this->transaction->payin_id = $this->payin->id;
        $this->transaction->payin_status_at = now();
        $this->transaction->payin_status = Transaction::pending;

        $this->transaction->user->profile->transactions_count += 1;
        $this->transaction->user->profile->save();

        try {
            $wallet = $this->transaction->wallet_transaction->user_wallet;
            decrypt_balance($wallet, $this->transaction->user);
            
            $wallet->balance -= unformat_money($this->transaction->send_amount);
            $wallet->balance_hash = encrypt_balance($wallet, $this->transaction->user);
            $wallet->save();

        } catch (\Exception $e) {
            $this->transaction->status = Transaction::failed;
            $this->transaction->payin_status = Transaction::failed;
            $this->transaction->note = 'Error: System could not successfully charge wallet';
            $this->transaction->save();
            Log::alert('WALLET TRANSACTION ERROR: ', [$e]);
            return response_beam()->throwOops('Transaction failed: System could not successfully charge wallet', 400);
        }

        $this->transaction->payin_status = Transaction::successful;
        $this->transaction->save();

        $this->sendWalletTransactionNotification($this->transaction, $this->transaction->user);

        $data = [
            'plugin' => 'via_wallet',
            'status' => 'success',
            'send_amount' => $this->transaction->send_amount,
            'reference' => $this->transaction->reference,
            // 'transfer_details' => json_decode($this->payin->info, true),
            'transfer_details' => [
                "bsb" => null,
                "account_number" => $this->transaction->user->userPayId->bank_account_number,
                "account_name" => null,
                "bank_name" => null,
                "payID_email" => $this->transaction->user->userPayId->payid
            ],
        ];

        return $data;
    }
}
