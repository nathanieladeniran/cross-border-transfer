<?php

namespace Payins\PayId\Aus;

use App\Enums\WalletFundingEnum;
use App\Models\Transaction;
use Payins\BaseStart;

class Start extends BaseStart
{
    public function init($is_wallet_transaction)
    {
        if ($is_wallet_transaction) {
            return $this->initiate_wallet_transaction();
        }
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

        $this->transaction->save();

        $this->sendMailNotification($this->transaction, $this->transaction->user);

        $data = [
            'plugin' => 'bank_transfer',
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

    public function initiate_wallet_transaction()
    {
        $this->transaction->payin = $this->payin->inner_name;
        $this->transaction->payin_id = $this->payin->id;
        $this->transaction->payin_status_at = now();
        $this->transaction->payin_status = WalletFundingEnum::PENDING;

        $this->transaction->user->profile->save();

        $this->transaction->save();
        $this->sendWalletFundNotification($this->transaction, $this->transaction->user);

        $data = [
            'plugin' => 'bank_transfer',
            'status' => 'success',
            'send_amount' => $this->transaction->sent_amount,
            'received_amount' => $this->transaction->received_amount,
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
