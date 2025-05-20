<?php

namespace App\Http\Controllers;

use App\Actions\UserTransactionActions;
use Illuminate\Http\Request;

class ApiTransactionController extends Controller
{
    public function allTransactions(Request $request)
    {
        $allTransaction = (new UserTransactionActions())->allUserTransactions($paginate = null, $per_page = null);
        return $allTransaction;
    }

    public function searchTransactions(Request $request)
    {
        $fetchTransaction = (new UserTransactionActions())->searchTransactions($request);
        return $fetchTransaction;
    }

    public function filterWithDate(Request $request)
    {
        $fetchTransaction = (new UserTransactionActions())->filterTransactionsWithDate($request);
        return $fetchTransaction;
    }
}
