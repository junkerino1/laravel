<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = \App\Models\User::class;


    public function definition()
    {
        return [
            'username' => $this->faker->userName,
            'password' => Hash::make('abcd1234'), // Encrypt 'abcd1234'
            'user_type' => 'user', // Default value
            'remember_token' => null,
        ];
    }
}
