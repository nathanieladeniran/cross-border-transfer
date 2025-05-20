<?php

namespace Payins\WalletPayins\CurrencyCloud;

use App\Enums\WalletFundingEnum;
use Payins\WalletPayins\BaseStart;

class Start extends BaseStart
{
    public function init()
    {
        return $this->initiate_transaction();
    }

    public function initiate_transaction()
    {
        $this->wallet_funding->payin_status_at = now();
        $this->wallet_funding->payin_status = WalletFundingEnum::PENDING;

        // $this->wallet_funding->user->profile->walletbalance += $this->wallet_funding->received_amount;
        $this->wallet_funding->user->profile->save();

        $this->wallet_funding->save();

        $this->sendMailNotification($this->wallet_funding, $this->wallet_funding->user);

        $data = [
            'plugin' => 'bank_transfer',
            'status' => 'success',
            'send_amount' => $this->wallet_funding->sent_amount,
            'received_amount' => $this->wallet_funding->received_amount,
            'reference' => $this->wallet_funding->reference,
            'transfer_details' => json_decode($this->payin->info, true),
        ];

        return $data;
    }
}
