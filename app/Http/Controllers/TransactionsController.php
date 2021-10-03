<?php

namespace App\Http\Controllers;

use App\Models\transactions;
use App\Models\chores;
use Illuminate\Http\Request;

class TransactionsController extends Controller
{
    /**
     * Display the collection of transactions
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    public function getUsersMostRecentTransaction(Request $request)
    {
        return transactions::where('user_id', $request->assigneeId)->orderBy('created_at', 'desc')->first();
    }

    /**
     * This gets the user transactions ordered by creation time for the user
     * of the request, not necessarily the currently logged in user.
     */
    public function getUserTransactionsOrderedByCreationTime(Request $request)
    {
        return transactions::where('user_id', $request->assigneeId)->orderBy('created_at', 'desc')->get();
    }

    /**
     * Store a new transaction in the transation list
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userTransactions = $this->getUserTransactionsOrderedByCreationTime($request);
        $mostRecentTransaction = $userTransactions->first();
        if ($mostRecentTransaction) {
            $usersPoints = $mostRecentTransaction->user_points;
        } else {
            $usersPoints = 0;
        }

        $chore = chores::find($request->id)->load('user');

        $transaction = new transactions;
        $transaction->user_id = $request->assigneeId;
        $transaction->event = "Completed chore $chore->chore";
        $transaction->user_points = $usersPoints + $chore->pointvalue;
        $transaction->occurred_at = now();
        $transaction->created_at = now();
        $transaction->updated_at = now();

        $transaction->save();

        return $this->getUserTransactionsOrderedByCreationTime($request);
    }

    /**
     * Update an instance of a transaction
     */
    public function update(Request $request, $transactionId)
    {

    }

    /**
     * Remove the transaction from storage.
     * 
     */
    public function destroy()
    {

    }
}
