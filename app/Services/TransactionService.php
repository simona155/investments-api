<?php

namespace App\Services;

use App\Exceptions\BusinessRuleException;
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

    private function getCashBalance(Account $account): float
    {
        return (float) $account
            ->transactions()
            ->selectRaw("
            COALESCE(SUM(
                CASE
                    WHEN type = 'deposit' THEN amount
                    WHEN type = 'withdrawal' THEN -amount
                    WHEN type = 'buy' THEN -amount
                    WHEN type = 'sell' THEN amount
                    ELSE 0
                END
            ), 0) AS balance
        ")
            ->value('balance');
    }

    public function withdraw(Account $account, float $amount): Transaction
    {
        $balance = $this->getCashBalance($account);

        if ($amount > $balance) {
            throw new BusinessRuleException(
                'Немате доволно средства на сметката.'
            );
        }

        return Transaction::create([
            'account_id' => $account->id,
            'type' => 'withdrawal',
            'amount' => $amount,
        ]);
    }
}
