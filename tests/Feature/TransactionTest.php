<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Client;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_deposit_creates_transaction(): void
    {
        $client = Client::create([
            'name' => 'Test Client',
        ]);

        $account = Account::create([
            'client_id' => $client->id,
            'currency' => 'EUR',
        ]);

        $response = $this->postJson('/api/transactions', [
            'account_id' => $account->id,
            'type' => 'deposit',
            'amount' => 1000,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('transactions', [
            'account_id' => $account->id,
            'type' => 'deposit',
            'amount' => 1000,
        ]);
    }

    public function test_withdrawal_creates_transaction(): void
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

        $response = $this->postJson('/api/transactions', [
            'account_id' => $account->id,
            'type' => 'withdrawal',
            'amount' => 300,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('transactions', [
            'account_id' => $account->id,
            'type' => 'withdrawal',
            'amount' => 300,
        ]);
    }

    public function test_withdrawal_is_rejected_when_insufficient_funds(): void
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

        $response = $this->postJson('/api/transactions', [
            'account_id' => $account->id,
            'type' => 'withdrawal',
            'amount' => 1200,
        ]);

        $response->assertStatus(422);

        $response->assertJson([
            'message' => 'Немате доволно средства на сметката.',
        ]);

        $this->assertDatabaseMissing('transactions', [
            'account_id' => $account->id,
            'type' => 'withdrawal',
            'amount' => 1200,
        ]);
    }
}
