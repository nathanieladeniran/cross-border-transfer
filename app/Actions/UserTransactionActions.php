<?php

namespace App\Actions;

use App\Http\Resources\UserTransactionResources;
use App\Models\Transaction;
use App\Traits\HasJsonResponse;

class UserTransactionActions
{
    use HasJsonResponse;
    /**
     * Create a new class instance.
     */

    public function allUserTransactions($paginate = null, $per_page = null)
    {
        $user = current_user();
        $transactions = Transaction::where('user_id', $user->id)
            ->with([
                'from_country',
                'to_country',
                'account' => function ($data) {
                    $data->withTrashed()
                        ->with(['beneficiary' => function ($data) {
                            $data->withTrashed();
                        }]);
                },
                'user',
                'payintype',
            ])->latest()
            ->paginate($per_page ?? 10);
        return $transactions;
        $transactions = UserTransactionResources::collection($transactions)->response()->getData(true);

        $this->jsonResponse(HTTP_SUCCESS, 'Transactions', $transactions);
    }

    public function searchTransactions($request)
    {
        $user = current_user();
        $transactions = Transaction::where('user_id', $user->id)
            ->where(function ($qr) use ($request) {
                $qr->whereNotNull('meta')
                    ->where('meta->account->account_name', 'LIKE', '%' . $request->param . '%');
            })
            ->with([
                'from_country',
                'to_country',
                'account' => function ($data) {
                    $data->withTrashed()
                        ->with(['beneficiary' => function ($data) {
                            $data->withTrashed();
                        }]);
                },
                'user',
                'payintype',
            ])->latest()->paginate(10);
        return $transactions;

        $transactions = UserTransactionResources::collection($transactions)->response()->getData(true);

        $this->jsonResponse(HTTP_SUCCESS, 'Transactions', $transactions);
    }

    public function filterTransactionsWithDate($request)
    {
        $user = current_user();
        $transactions = Transaction::query();

        $transactions->where('user_id', $user->id);

        if (!empty($request->param)) {
            $transactions->where(function ($qr) use ($request) {
                $qr->whereDate('created_at', $request->param)
                    ->orWhereDate('completed_at', $request->param);
            });
        }

        $transactions = $transactions->with([
            'from_country',
            'to_country',
            'account' => function ($data) {
                $data->withTrashed()
                    ->with(['beneficiary' => function ($data) {
                        $data->withTrashed();
                    }]);
            },
            'user',
            'payintype',
        ])->latest()->paginate(10);

        return $transactions;
        $transactions = UserTransactionResources::collection($transactions)->response()->getData(true);

        $this->jsonResponse(HTTP_SUCCESS, 'Transactions', $transactions);
    }
}
