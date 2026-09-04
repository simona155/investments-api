<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Client;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $client1 = Client::create([
            'name' => 'John Smith',
        ]);

        $account1 = Account::create([
            'client_id' => $client1->id,
            'currency' => 'EUR',
        ]);

        Transaction::create([
            'account_id' => $account1->id,
            'type' => 'deposit',
            'amount' => 5000,
        ]);

        Transaction::create([
            'account_id' => $account1->id,
            'type' => 'buy',
            'amount' => 2000,
            'instrument' => 'AAPL',
            'quantity' => 10,
            'price' => 200,
        ]);

        Transaction::create([
            'account_id' => $account1->id,
            'type' => 'buy',
            'amount' => 1500,
            'instrument' => 'MSFT',
            'quantity' => 5,
            'price' => 300,
        ]);

        Transaction::create([
            'account_id' => $account1->id,
            'type' => 'withdrawal',
            'amount' => 500,
        ]);

        $client2 = Client::create([
            'name' => 'Anna Johnson',
        ]);

        $account2 = Account::create([
            'client_id' => $client2->id,
            'currency' => 'EUR',
        ]);

        Transaction::create([
            'account_id' => $account2->id,
            'type' => 'deposit',
            'amount' => 3000,
        ]);

        Transaction::create([
            'account_id' => $account2->id,
            'type' => 'buy',
            'amount' => 1500,
            'instrument' => 'TSLA',
            'quantity' => 10,
            'price' => 150,
        ]);

        Transaction::create([
            'account_id' => $account2->id,
            'type' => 'sell',
            'amount' => 540,
            'instrument' => 'TSLA',
            'quantity' => 3,
            'price' => 180,
        ]);
    }
}
