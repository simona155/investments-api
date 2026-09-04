<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_balance_is_calculated_from_transactions(): void
    {
        $client = Client::create([
            'name' => 'Test Client',
        ]);

        $account = Account::create([
            'client_id' => $client->id,
            'currency' => 'EUR',
        ]);

        $this->postJson('/api/transactions', [
            'account_id' => $account->id,
            'type' => 'deposit',
            'amount' => 1000,
        ]);

        $this->postJson('/api/transactions', [
            'account_id' => $account->id,
            'type' => 'buy',
            'instrument' => 'AAPL',
            'quantity' => 10,
            'price' => 20,
        ]);

        $this->postJson('/api/transactions', [
            'account_id' => $account->id,
            'type' => 'sell',
            'instrument' => 'AAPL',
            'quantity' => 2,
            'price' => 30,
        ]);

        $this->postJson('/api/transactions', [
            'account_id' => $account->id,
            'type' => 'withdrawal',
            'amount' => 100,
        ]);

        $response = $this->getJson("/api/accounts/{$account->id}/balance");

        $response
            ->assertStatus(200)
            ->assertJson([
                'balance' => 760,
            ]);
    }

    public function test_holdings_are_calculated_from_transactions(): void
    {
        $client = Client::create([
            'name' => 'Test Client',
        ]);

        $account = Account::create([
            'client_id' => $client->id,
            'currency' => 'EUR',
        ]);

        $this->postJson('/api/transactions', [
            'account_id' => $account->id,
            'type' => 'deposit',
            'amount' => 1000,
        ]);

        $this->postJson('/api/transactions', [
            'account_id' => $account->id,
            'type' => 'buy',
            'instrument' => 'AAPL',
            'quantity' => 10,
            'price' => 20,
        ]);

        $this->postJson('/api/transactions', [
            'account_id' => $account->id,
            'type' => 'sell',
            'instrument' => 'AAPL',
            'quantity' => 3,
            'price' => 30,
        ]);

        $this->postJson('/api/transactions', [
            'account_id' => $account->id,
            'type' => 'buy',
            'instrument' => 'MSFT',
            'quantity' => 5,
            'price' => 20,
        ]);

        $response = $this->getJson("/api/accounts/{$account->id}/holdings");

        $response
            ->assertStatus(200)
            ->assertJson([
                'holdings' => [
                    'AAPL' => 7,
                    'MSFT' => 5,
                ],
            ]);
    }

    public function test_accounts_are_isolated(): void
    {
        $client1 = Client::create([
            'name' => 'Client One',
        ]);

        $account1 = Account::create([
            'client_id' => $client1->id,
            'currency' => 'EUR',
        ]);

        $client2 = Client::create([
            'name' => 'Client Two',
        ]);

        $account2 = Account::create([
            'client_id' => $client2->id,
            'currency' => 'EUR',
        ]);

        $this->postJson('/api/transactions', [
            'account_id' => $account1->id,
            'type' => 'deposit',
            'amount' => 1000,
        ]);

        $this->postJson('/api/transactions', [
            'account_id' => $account2->id,
            'type' => 'deposit',
            'amount' => 500,
        ]);

        $response = $this->getJson("/api/accounts/{$account1->id}/balance");

        $response
            ->assertStatus(200)
            ->assertJson([
                'balance' => 1000,
            ]);
    }
}
