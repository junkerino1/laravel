<?php

namespace App\Http\Controllers;

use App\Models\Gift;
use App\Models\GiftRecord;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class GiftController extends Controller
{

    public function cronCheckGift(){

        $users = User::all();
        foreach($users as $user){
            $this->checkGift($user->id);
        }

    }

    public function checkGift()
    {
        $user_id = auth()->id();
        $type = 'total_deposit';

        // Step 1: Find the user's wallet
        $wallet = Wallet::where('user_id', '=', $user_id)->first();
        if (!$wallet) {
            throw new \Exception('Wallet not found.');
        }

        // Step 2: Calculate today's transaction sum
        $date = now()->format('Y-m-d');

        // Step 3: Fetch all gift conditions
        $gifts = Gift::all();
        $giftHolder = [];

        $transactions = DB::table('transactions')
            ->where('wallet_id', $wallet->id)
            ->whereBetween('created_at', [$date . ' 00:00:00', now()])
            ->get();


        $transactionSum = 0;

        foreach ($transactions as $transaction) {
            switch ($type) {
                case 'total_deposit':
                    $transactionSum += $transaction->amount;
                    break;
                case 'per_transaction':
                    $transactionSum = $transaction->amount;
                    break;
            }
        }

         // Step 4: Check if transaction sum fulfills conditions
         foreach ($gifts as $gift) {
            if ($transactionSum >= $gift->condition) {
                $giftHolder[] = $gift; // Push the gift_id
            }
        }

        // Step 5: Fetch gift records already issued to the user
        $existingGiftRecords = GiftRecord::where('user_id', $user_id)
            ->whereDate('issue_date', $date) // Check if already issued today
            ->pluck('gift_id') // Get only the gift_ids
            ->toArray();

        // Step 6: Issue new gift records if not already issued
        foreach ($giftHolder as $gift_id) {
            if (!in_array($gift_id, $existingGiftRecords)) {
                GiftRecord::create([
                    'user_id' => $user_id,
                    'gift_id' => $gift_id,
                    'issue_date' => now()->format('Y-m-d'),
                ]);
            }
        }
        return back()->with([
            'gift_message' => 'Gift issued successfully',
            'gift_issued' => $giftHolder,
        ]);
    }
}
