<?php

namespace Payins\WalletPayins;

use App\Models\Payin;
use App\Models\WalletFunding;
use App\Notifications\AwaitingTransactionNotification;
use App\Notifications\PaymentPendingTransaction;
use Illuminate\Support\Facades\Notification;

abstract class BaseStart
{
    protected $wallet_funding;

    protected $payin;

    public function boot(WalletFunding $wallet_funding, Payin $payin)
    {
        $this->wallet_funding = $wallet_funding;
        $this->payin = $payin;

        return $this->init();
    }

    abstract public function init();

    public function sendMailNotification($finish, $user)
    {
        $user->notify(new PaymentPendingTransaction($finish, true));
        Notification::route('mail', 'transactions@cosmoremit.com.au')
            ->notify(new AwaitingTransactionNotification($finish, true));
    }
}
