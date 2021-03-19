<?php

namespace App\Http\Controllers;

use App\Models\transactions;
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

    public function getUsersMostRecentTransaction($userId)
    {
        return transactions::where('user_id', $userId)->orderBy('created_at', 'desc')->first();
    }

    /**
     * Store a new transaction in the transation list
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $chore, $accumulatedPoints)
    {
        // Hrm, for some reason, updatedChore is empty...

        $transaction = new transactions;
        $transaction->user_id = $request->assigneeId;
        $transaction->event = "Completed chore $chore->chore";
        $transaction->user_points = $accumulatedPoints + $chore->pointvalue;
        $transaction->occurred_at = now();
        $transaction->created_at = now();
        $transaction->updated_at = now();

        $transaction->save();
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
