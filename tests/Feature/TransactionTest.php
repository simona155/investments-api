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
}
