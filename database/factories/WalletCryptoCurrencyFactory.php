<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WalletCryptoCurrency>
 */
class WalletCryptoCurrencyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'currency' => $this->faker->randomElement(['bitcoin', 'litecoin', 'ethereum']), //crypto wallet currency to fund
            'balance' => $this->faker->randomDigit(1, 30),
            'wallet_id' => $this->faker->randomDigitNotZero(),
        ];
    }
}
