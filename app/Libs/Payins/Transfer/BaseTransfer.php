<?php

namespace App\Libs\Payins\Transfer;

use App\Enums\WalletFundingEnum;

class BaseTransfer extends \Payins\BaseStart
{

    public function init($is_wallet_transaction): array
    {
        if ($is_wallet_transaction) {
            return $this->initiate_wallet_transaction();
        }
        return $this->initiate_transaction();
    }

    public function initiate_transaction(): array
    {
        $this->transaction->payin = $this->payin->inner_name; //confirm inner_name
        $this->transaction->payin_id = $this->payin->id;
        $this->transaction->payin_status_at = now();
        $this->transaction->payin_status = 'pending';
        $this->transaction->save();

        return [
            'plugin' => 'bank_transfer',
            'status' => 'success',
            'send_amount' => $this->transaction->send_amount,
            'reference' => $this->transaction->reference,
            'transfer_details' => json_decode($this->payin->info, true),
        ];

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
            'transfer_details' => json_decode($this->payin->info, true),
        ];

        return $data;
    }
    
}
