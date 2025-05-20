<?php

namespace Payins\WalletPayins\Transfer;

class BaseTransfer extends \Payins\WalletPayins\BaseStart
{

    public function init(): array
    {
        return $this->initiate_transaction();
    }

    public function initiate_transaction(): array
    {
        $this->wallet_funding->payin = $this->payin->inner_name; //confirm inner_name
        $this->wallet_funding->payin_id = $this->payin->id;
        $this->wallet_funding->payin_status_at = now();
        $this->wallet_funding->payin_status = 'pending';
        $this->wallet_funding->save();

        return [
            'plugin' => 'bank_transfer',
            'status' => 'success',
            'send_amount' => $this->wallet_funding->sent_amount,
            'reference' => $this->wallet_funding->reference,
            'transfer_details' => json_decode($this->payin->info, true),
        ];

    }
}
