<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Log;
use Illuminate\Support\Carbon;
use DateTime;

class SlotBetRecordController extends Controller
{
    public function pgSoftJson()
    {
        // Simulate callback JSON
        $filePath = storage_path('app/public/pgsoft.json');
        if (!file_exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        // Read the file and decode JSON
        $jsonContent = file_get_contents($filePath);
        $data = json_decode($jsonContent, true);

        // Validations
        if (!is_array($data)) {
            return response()->json(['error' => 'Invalid JSON format'], 400);
        }

        // Extract and process fields
        $bet_id = $data['betId'];
        $player_name = $data['playerName'];
        $provider = 'pgsoft';
        $game_id = $data['gameId'];
        $currency = $data['currency'];
        $bet_amount = $data['betAmount'];
        $winloss_amount = $data['balanceAfter'] - $data['balanceBefore'];
        if ($winloss_amount > 0) {
            $status = 'win';
        } elseif ($winloss_amount < 0) {
            $status = 'loss';
        } else {
            $status = 'draw';
        }
        $wallet = 'pgsoft_wallet';
        $processed = 0; // Default processed value
        $bet_date = Carbon::createFromTimestamp($data['betEndTime'] / 1000)->format('Y-m-d H:i:s');

        // Prepare data for insertion
        $query = [
            'bet_id' => $bet_id,
            'player_name' => $player_name,
            'currency' => $currency,
            'game_id' => $game_id,
            'provider' => $provider,
            'data' => $jsonContent,
            'winloss_amount' => $winloss_amount,
            'bet_amount' => $bet_amount,
            'bet_date' => $bet_date,
            'status' => $status,
            'processed' => $processed,
            'wallet' => $wallet,
        ];

        // Insert data into the database
        try {
            Log::create($query);
            return response()->json(['message' => 'Data stored successfully!', 'log' => $query], 201);
        } catch (\Exception $e) {
            // Handle exceptions gracefully
            return response()->json(['error' => 'Failed to store data', 'details' => $e->getMessage()], 500);
        }
    }

    public function pragmaticJson()
    {
        // Simulate callback JSON
        $filePath = storage_path('app/public/pragmatic.json');
        if (!file_exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        // Read the file and decode JSON
        $jsonContent = file_get_contents($filePath);
        $data = json_decode($jsonContent, true);

        // Validations
        if (!is_array($data)) {
            return response()->json(['error' => 'Invalid JSON format'], 400);
        }

        // Extract and process fields
        $bet_id = $data['playSessionID'];
        $player_name = $data['playerID'];
        $provider = 'pragmaticplay';
        $game_id = $data['gameID'];
        $currency = $data['currency'];
        $bet_amount = $data['bet'];

        $winloss_amount = $data['win'] - $data['bet_amount'];
        if ($winloss_amount > 0) {
            $status = 'win';
        } elseif ($winloss_amount < 0) {
            $status = 'loss';
        } else {
            $status = 'draw';
        }
        $wallet = 'pragmaticplay_wallet';
        $processed = 0; // Default processed value
        $bet_date = $data['endDate'];

        $query = [
            'bet_id' => $bet_id,
            'player_name' => $player_name,
            'currency' => $currency,
            'game_id' => $game_id,
            'provider' => $provider,
            'data' => $jsonContent,
            'winloss_amount' => $winloss_amount,
            'bet_amount' => $bet_amount,
            'bet_date' => $bet_date,
            'status' => $status,
            'processed' => $processed,
            'wallet' => $wallet,
        ];

        // Insert data into the database
        try {
            Log::create($query);
            return response()->json(['message' => 'Data stored successfully!', 'log' => $query], 201);
        } catch (\Exception $e) {
            // Handle exceptions gracefully
            return response()->json(['error' => 'Failed to store data', 'details' => $e->getMessage()], 500);
        }
    }

    public function nextspinJson()
    {
        // Simulate callback JSON
        $filePath = storage_path('app/public/nextspin.json');
        if (!file_exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        // Read the file and decode JSON
        $jsonContent = file_get_contents($filePath);
        $data = json_decode($jsonContent, true);

        // Validations
        if (!is_array($data)) {
            return response()->json(['error' => 'Invalid JSON format'], 400);
        }

        // Extract and process fields
        $bet_id = $data['ticketId'];
        $player_name = $data['acctId'];
        $provider = 'nextspin';
        $game_id = $data['gameCode'];
        $currency = $data['currency'];
        $bet_amount = $data['betAmount'];
        $winloss_amount = $data['winLoss'];
        if ($winloss_amount > 0) {
            $status = 'win';
        } elseif ($winloss_amount < 0) {
            $status = 'loss';
        } else {
            $status = 'draw';
        }
        $wallet = 'nextspin_wallet';
        $processed = 0; // Default processed value
        $bet_date = DateTime::createFromFormat('Ymd\THis', $data['createTime'])->format('Y:m:d H:i:s');

        $query = [
            'bet_id' => $bet_id,
            'player_name' => $player_name,
            'currency' => $currency,
            'game_id' => $game_id,
            'provider' => $provider,
            'data' => $jsonContent,
            'winloss_amount' => $winloss_amount,
            'bet_amount' => $bet_amount,
            'bet_date' => $bet_date,
            'status' => $status,
            'processed' => $processed,
            'wallet' => $wallet,
        ];

        // Insert data into the database
        try {
            Log::create($query);
            return response()->json(['message' => 'Data stored successfully!', 'log' => $query], 201);
        } catch (\Exception $e) {
            // Handle exceptions gracefully
            return response()->json(['error' => 'Failed to store data', 'details' => $e->getMessage()], 500);
        }
    }

    public function habaneroJson()
    {
        // Simulate callback JSON
        $filePath = storage_path('app/public/habanero.json');
        if (!file_exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        // Read the file and decode JSON
        $jsonContent = file_get_contents($filePath);
        $data = json_decode($jsonContent, true);

        // Validations
        if (!is_array($data)) {
            return response()->json(['error' => 'Invalid JSON format'], 400);
        }

        // Extract and process fields
        $bet_id = $data['ticketId'];
        $player_name = $data['acctId'];
        $provider = 'nextspin';
        $game_id = $data['gameCode'];
        $currency = $data['currency'];
        $bet_amount = $data['betAmount'];
        $winloss_amount = $data['winLoss'];
        if ($winloss_amount > 0) {
            $status = 'win';
        } elseif ($winloss_amount < 0) {
            $status = 'loss';
        } else {
            $status = 'draw';
        }
        $wallet = 'habanero_wallet';
        $processed = 0; // Default processed value
        $bet_date = DateTime::createFromFormat('Ymd\THis', $data['createTime'])->format('Y:m:d H:i:s');

        $query = [
            'bet_id' => $bet_id,
            'player_name' => $player_name,
            'currency' => $currency,
            'game_id' => $game_id,
            'provider' => $provider,
            'data' => $jsonContent,
            'winloss_amount' => $winloss_amount,
            'bet_amount' => $bet_amount,
            'bet_date' => $bet_date,
            'status' => $status,
            'processed' => $processed,
            'wallet' => $wallet,
        ];

        // Insert data into the database
        try {
            Log::create($query);
            return response()->json(['message' => 'Data stored successfully!', 'log' => $query], 201);
        } catch (\Exception $e) {
            // Handle exceptions gracefully
            return response()->json(['error' => 'Failed to store data', 'details' => $e->getMessage()], 500);
        }
    }
}
