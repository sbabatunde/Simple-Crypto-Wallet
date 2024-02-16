<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WalletApiTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_new_crypto_users_can_register(): void
    {
        $response = $this->post(route('user.register'), [
            'phone' => '08051835090',
            'password' => 'password',
        ]);

        $response->assertStatus(200);
    }

    // public function test_user_can_create_new_crypto_wallet(): void
    // {
    //     $user = User::factory()->create();
    //     $this->actingAs($user);
    //     $response = $this->post(route('wallet.create'), [
    //         'wallet_name' => 'bitcoin',
    //         'email' => 'stunde@gmail.com',
    //         'user_id' => $user->id,
    //     ]);

    //     $response->assertStatus(200);
    // }

    public function test_user_can_fund_wallet(): void
    {
        $user = User::factory()->create();
        $transaction = Transaction::factory()->create();
        $this->actingAs($user);
        $response = $this->post(route('wallet.fund'), [
            'amount' => $transaction->tx_amount,
            'currency' => $transaction->rx_symbol,
            'rx_email' => 'stunde@gmail.com',
        ]);

        $this->withoutExceptionHandling();
        $response->assertStatus(200);
    }
}
