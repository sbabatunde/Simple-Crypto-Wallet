<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'wallet_id' => $this->faker->randomDigitNotZero(),
            'tx_amount' => $this->faker->randomDigit(10000, 50000),
            'rx_email' => $this->faker->email(),
            'tx_type' => $this->faker->randomElement(['deposit', 'transfer']),
            'rx_amount' => $this->faker->randomDigit(1, 30),
            'tx_symbol' => $this->faker->randomElement(['usd', 'ngn']),
            'rx_symbol' => $this->faker->randomElement(['bitcoin', 'litecoin', 'ethereum']),
        ];
    }
}
