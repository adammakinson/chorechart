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
    public function store(Request $request)
    {

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
