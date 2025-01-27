<?php

namespace Database\Factories;

use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class WalletFactory extends Factory
{
    protected $model = Wallet::class;

    public function definition()
    {
        return [
            'user_id' => $this->faker->unique()->numberBetween(1, 1000),
            'amount' => 10000,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
