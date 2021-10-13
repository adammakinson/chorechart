<?php

namespace App\Http\Controllers;

use App\Models\transactions;
use App\Models\chores;
use Illuminate\Http\Request;
use App\Models\Reward;

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

        switch($request->transactionType) {
            case 'choreCompletion':
                $this->createTransactionAwardingPoints($request, $usersPoints);
                break;
            
            case 'rewardPurchase': 
                if ($usersPoints >= $request->point_value) {
                    $this->createTransactionSpendingPoints($request, $usersPoints);
                } else {
                    return response('You don\'t have enough points for that reward', 403)->header('Content-Type', 'application/json');
                }
                break;
        }

        return $this->getUserTransactionsOrderedByCreationTime($request);
    }

    private function createTransactionAwardingPoints(Request $request, $usersPoints)
    {
        $chore = chores::find($request->id)->load('user');

        $transaction = new transactions;
        $transaction->user_id = $request->assigneeId;
        $transaction->event = "Completed chore $chore->chore";
        $transaction->user_points = $usersPoints + $chore->pointvalue;
        $transaction->occurred_at = now();
        $transaction->created_at = now();
        $transaction->updated_at = now();

        $transaction->save();
    }

    private function createTransactionSpendingPoints(Request $request, $usersPoints)
    {
        $reward = Reward::find($request->id);

        $transaction = new transactions;
        $transaction->user_id = $request->assigneeId;
        $transaction->event = "$reward->point_value points spent on $reward->reward";
        $transaction->user_points = $usersPoints - $reward->point_value;
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
