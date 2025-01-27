<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReceiptFactory extends Factory
{
    public function definition()
    {
        $winlost = ['win', 'lost'];

        $apiUsernames = DB::table('user_wallet')->pluck('api_username')->toArray();

        return [
            'transaction_id' => $this->faker->regexify('[A-Z0-9]{6}'),
            'api_username' => $this->faker->randomElement($apiUsernames), // Allow duplicates
            'amount' => $this->faker->randomFloat(2, 1, 1000), // Random amount between 1 and 10,000
            'winlost' => $this->faker->randomElement($winlost),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
