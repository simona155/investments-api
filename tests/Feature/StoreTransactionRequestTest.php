<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTransactionRequestTest extends TestCase
{
    use RefreshDatabase;

    private function createAccount(): Account
    {
        $client = Client::create([
            'name' => 'Test Client',
        ]);

        return Account::create([
            'client_id' => $client->id,
            'currency' => 'EUR',
        ]);
    }

    public function test_transaction_type_is_required(): void
    {
        $account = $this->createAccount();

        $response = $this->postJson('/api/transactions', [
            'account_id' => $account->id,
            'amount' => 100,
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['type']);
    }

    public function test_transaction_type_must_be_valid(): void
    {
        $account = $this->createAccount();

        $response = $this->postJson('/api/transactions', [
            'account_id' => $account->id,
            'type' => 'invalid',
            'amount' => 100,
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['type']);
    }

    public function test_deposit_amount_must_be_positive(): void
    {
        $account = $this->createAccount();

        $response = $this->postJson('/api/transactions', [
            'account_id' => $account->id,
            'type' => 'deposit',
            'amount' => 0,
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['amount']);
    }

    public function test_buy_requires_instrument(): void
    {
        $account = $this->createAccount();

        $response = $this->postJson('/api/transactions', [
            'account_id' => $account->id,
            'type' => 'buy',
            'quantity' => 10,
            'price' => 20,
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['instrument']);
    }

    public function test_buy_quantity_must_be_positive_integer(): void
    {
        $account = $this->createAccount();

        $response = $this->postJson('/api/transactions', [
            'account_id' => $account->id,
            'type' => 'buy',
            'instrument' => 'AAPL',
            'quantity' => 0,
            'price' => 20,
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['quantity']);
    }

    public function test_buy_price_must_be_positive(): void
    {
        $account = $this->createAccount();

        $response = $this->postJson('/api/transactions', [
            'account_id' => $account->id,
            'type' => 'buy',
            'instrument' => 'AAPL',
            'quantity' => 10,
            'price' => 0,
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['price']);
    }

    public function test_sell_requires_instrument_quantity_and_price(): void
    {
        $account = $this->createAccount();

        $response = $this->postJson('/api/transactions', [
            'account_id' => $account->id,
            'type' => 'sell',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'instrument',
                'quantity',
                'price',
            ]);
    }
}
