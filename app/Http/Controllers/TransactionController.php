<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function transactionHistory()
    {
        //get all transactions
        $allTransactions = Transaction::with(['wallet.user' => function ($q) {
            $q->where('wallet.user.user_id', Auth::id());
        }])->get();

        return response()->json(['data' => $allTransactions]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function fundWallet()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function transferFund(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function convertCoin(Transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
