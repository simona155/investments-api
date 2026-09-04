<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;

class TransactionService
{
    public function deposit(Account $account, float $amount): Transaction
    {
        return Transaction::create([
            'account_id' => $account->id,
            'type' => 'deposit',
            'amount' => $amount,
        ]);
    }
}
