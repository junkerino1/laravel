<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function credit(Request $request)
    {
        $user_id = auth()->id();

        $validated = $request->validate([
            'amount' => 'required|integer',
        ]);

        $wallet = Wallet::where('user_id', $user_id)->first();

        if (!$wallet) {
            $wallet = Wallet::create([
                'user_id' => $user_id,
                'amount' => 0
            ]);
        }

        $wallet->amount += $validated['amount'];
        $wallet->save();

        Transaction::create([
            'wallet_id' => $wallet->id,
            'amount' => $validated['amount'],
            'transaction_type' => 'credit',
            'source' => 'topup',
        ]);

        return back()->with([
            'message' => 'Wallet debited successfully',
            'wallet_balance' => $wallet->amount,
        ]);
    }

    public function checkBalance()
    {

        $user_id = auth()->id();

        $balance = Wallet::where('user_id', '=', $user_id)
        ->value('amount');

        return view('gift', ['wallet_balance' => $balance]);
    }
}
