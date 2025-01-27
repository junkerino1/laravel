<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\UserWallet;

class UserWalletFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserWallet::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        static $apiUsernames = [];

        // Ensure unique api_username
        do {
            $apiUsername = $this->faker->regexify('[A-Z0-9]{6}');
        } while (in_array($apiUsername, $apiUsernames));

        $apiUsernames[] = $apiUsername;

        return [
            'user_id' => $this->faker->numberBetween(1,10001),
            'api_username' => $apiUsername,
            'amount' => 100.00,
        ];
    }
}
