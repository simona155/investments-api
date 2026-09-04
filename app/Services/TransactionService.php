<?php

namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Models\Account;
use App\Models\Transaction;

class TransactionService
{
    public function __construct(
        private AccountBalanceService $accountBalanceService
    ) {}

    public function deposit(Account $account, float $amount): Transaction
    {
        return Transaction::create([
            'account_id' => $account->id,
            'type' => 'deposit',
            'amount' => $amount,
        ]);
    }



    public function withdraw(Account $account, float $amount): Transaction
    {
        $balance = $this->accountBalanceService->getBalance($account);

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

    public function buy(
        Account $account,
        string $instrument,
        int $quantity,
        float $price
    ): Transaction {
        $amount = $quantity * $price;

        $balance = $this->accountBalanceService->getBalance($account);

        if ($amount > $balance) {
            throw new BusinessRuleException(
                'Немате доволно средства на сметката.'
            );
        }

        return Transaction::create([
            'account_id' => $account->id,
            'type' => 'buy',
            'amount' => $amount,
            'instrument' => $instrument,
            'quantity' => $quantity,
            'price' => $price,
        ]);
    }

    private function getHolding(Account $account, string $instrument): int
    {
        return (int) $account
            ->transactions()
            ->where('instrument', $instrument)
            ->selectRaw("
            COALESCE(SUM(
                CASE
                    WHEN type = 'buy' THEN quantity
                    WHEN type = 'sell' THEN -quantity
                    ELSE 0
                END
            ), 0) AS holding
        ")
            ->value('holding');
    }

    public function sell(
        Account $account,
        string $instrument,
        int $quantity,
        float $price
    ): Transaction {
        $holding = $this->getHolding($account, $instrument);

        if ($quantity > $holding) {
            throw new BusinessRuleException(
                'Немате доволно единици за продажба.'
            );
        }

        $amount = $quantity * $price;

        return Transaction::create([
            'account_id' => $account->id,
            'type' => 'sell',
            'amount' => $amount,
            'instrument' => $instrument,
            'quantity' => $quantity,
            'price' => $price,
        ]);
    }
}
