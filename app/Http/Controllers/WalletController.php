<?php

namespace App\Http\Controllers;

use App\Classes\BlockChain;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\WalletRequest;
use App\Models\WalletCryptoCurrency;
use App\Http\Requests\DepositRequest;
use App\Http\Requests\TransactionRequest;
use App\Http\Requests\TransferRequest;
use App\Models\Transaction;
use Codenixsv\CoinGeckoApi\CoinGeckoClient;
use Carbon\Carbon;
use DB;

class WalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function walletDashboard()
    {
        //get User's balance for each wallet

        $blockchain = new BlockChain;
        $client = new CoinGeckoClient();
        $data = $client->ping();
        $symbol = 'ngn';
        $coin = 'bitcoin';
        $deposit = 100;
        $result = $client->simple()->getPrice($coin, $symbol);
        // $usd = $result['usd'];
        // $usd_equiva = $result['usd'];
        foreach ($result as $data) {
            $result = $data['ngn'];
        }
        $balance = $deposit / (int)$result;
        return response()->json(['data' => $result, 'balance' => $balance]);
    }

    /**
     * Create new wallet for an authorized user.
     */
    public function createUserWallet(WalletRequest $request)
    {
        $myWallet = Wallet::where('user_id', Auth::id())->get();
        //check if user already has wallet
        if (!$myWallet->isEmpty()) {
            return response()->json(['data' => 'You already have an existing wallet']);
        } else {
            $wallet = Wallet::create([
                'email' => $request->input('email'),
                'user_id' => Auth::id()
            ]);
            // return user details with wallet
            return response()->json(['data' => 'Wallet Created Succesfully']);
        }
    }


    public function fundWallet(TransactionRequest $request)
    {
        $symbol = 'usd'; //assuming all transactions are done in naira
        $coin = $request->currency; //get coin type [bitcoin,litecoin,ethereum]

        //get live cryptocurrency rate to naira
        $client = new CoinGeckoClient();
        $result = $client->simple()->getPrice($coin, $symbol);
        foreach ($result as $data) {
            $result = $data['usd']; //get price of coin in us dollars
        }
        $amount = $request->amount;
        $balance = $amount / $result;
        $wallet_id = Wallet::where('user_id', Auth::user()->id)->value('user_id');

        //to check if crypto account already exists
        $user_account = Wallet::with(['currency' => function ($query) use ($coin, $wallet_id) {
            $query->where('currency', $coin)->where('wallet_id', $wallet_id);
        }])->get();
        // dd($user_account->isEmpty());
        //create new cryptocurrency for user wallet if the crypto account doesn't exist
        if ($user_account->isEmpty()) {
            $walletCrypto = WalletCryptoCurrency::create([
                'currency' => $coin, //crypto wallet currency to fund
                'balance' => $balance,
                'wallet_id' => $wallet_id
            ]);
        }

        // deposit into existing account
        else {

            $walletCrypto = WalletCryptoCurrency::where('wallet_id', $wallet_id)->where('currency', $coin)
                ->update(['balance' => DB::raw('balance+' . $balance)]);
        }

        $transaction = Transaction::create([
            'wallet_id' => $wallet_id,
            'tx_amount' => $amount,
            'rx_email' => $request->rx_email,
            'tx_type' => 'deposit',
            'rx_amount' => $balance,
            'tx_symbol' => $symbol,
            'rx_symbol' => $coin,
        ]);

        $wallet = Wallet::with(['currency' => function ($query) use ($coin) {
            $query->where('currency', $coin);
        }, 'user' => function ($query) {
            $query->where('id', Auth::id());
        }])->get();
        // $transaction = Transaction::latest();
        return response()->json(['wallet' => $wallet, 'transaction' => $transaction]);
    }

    public function transferFund(TransactionRequest $request)
    {
        $sender_currency = $request->currency;
        $amount = $request->amount;
        $recipient_email = $request->rx_email;

        //get wallet details for this sender
        $sender_wallet = Wallet::with(['currency' => function ($query) use ($sender_currency) {
            $query->where('currency', $sender_currency);
        }, 'user' => function ($query) {
            $query->where('id', Auth::id());
        }])->get();

        //get recipient's wallet's details

        $recipient_wallet = Wallet::with(['currency' => function ($query) use ($sender_currency) {
            $query->where('currency', $sender_currency);
        }])->where('email', $recipient_email)->get();

        if (!empty($sender_wallet)) //check if sender has the cryptocurrency wallet
        {

            if ($sender_wallet->balance < $amount) { //check if sender balance is sufficient for transaction
                return response()->json(['message' => 'Insfficient balance']);
            } elseif ($sender_wallet->currency !== $recipient_wallet->currency) { //check if the currency is the same
                return response()->json(['message' => 'Unsupported transaction, you can only transact to same currency']);
            } else {
                //deduct amount from sender's crypto wallet
                $sender_balance = WalletCryptoCurrency::where('wallet_id', $sender_wallet->wallet_id)->update(['balance' => DB::raw('balance-' . $amount)]);

                //add amount to recipient's crypto wallet
                $recipient_balance = WalletCryptoCurrency::where('wallet_id', $recipient_wallet->wallet_id)
                    ->where('currency', $sender_currency)->update([
                        'balance' => DB::raw('balance+' . $amount),
                    ]);

                $transaction = Transaction::create([
                    'wallet_id' => $sender_wallet->wallet_id,
                    'tx_amount' => $amount,
                    'rx_email' => $request->rx_email,
                    'tx_type' => 'deposit',
                    'rx_amount' => $amount,
                    'tx_symbol' => $sender_currency,
                    'rx_symbol' => $sender_currency,
                ]);

                return response()->json([
                    'message' => 'Transfer Successful', 'sender_balance' => $sender_balance,
                    'recipient_balance' => $recipient_balance
                ]);
            }
        } else {
            return response()->json(['message' => 'Unsupported transaction, create crypto wallet for this coin']);
        }
    }
}
