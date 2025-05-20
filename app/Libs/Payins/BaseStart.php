<?php

namespace Payins;

use App\Models\Payin;
use App\Notifications\AwaitingTransactionNotification;
use App\Notifications\PaymentPendingTransaction;
use App\Notifications\WalletFundTransaction;
use App\Notifications\WalletTransactionInitiationNotification;
use Illuminate\Support\Facades\Notification;
abstract class BaseStart
{
    protected $transaction;

    protected $payin;

    // public function boot(Transaction $transaction, Payin $payin)
    public function boot($transaction, $payin, bool $is_wallet_transaction=false)
    {
        $this->transaction = $transaction;
        $this->payin = $payin;

        return $this->init($is_wallet_transaction);
    }

    abstract public function init(bool $is_wallet_transaction);

    public function sendMailNotification($finish, $user)
    {
        $user->notify(new PaymentPendingTransaction($finish));
        Notification::route('mail', 'transactions@cosmoremit.com.au')
            ->notify(new AwaitingTransactionNotification($finish));
    }

    public function sendWalletFundNotification($finish, $user)
    {
        $user->notify(new WalletFundTransaction($finish));
    }

    public function sendWalletTransactionNotification($finish, $user)
    {
        $user->notify(new WalletTransactionInitiationNotification($finish));
    }
}
