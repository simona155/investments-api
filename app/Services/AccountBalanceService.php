<?php

namespace App\Services;

use App\Models\Account;

class AccountBalanceService
{
    public function getBalance(Account $account): float
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

    public function getHoldings(Account $account): array
    {
        return $account
            ->transactions()
            ->selectRaw("
                instrument,
                SUM(
                    CASE
                        WHEN type = 'buy' THEN quantity
                        WHEN type = 'sell' THEN -quantity
                        ELSE 0
                    END
                ) AS quantity
            ")
            ->whereNotNull('instrument')
            ->groupBy('instrument')
            ->havingRaw('quantity > 0')
            ->pluck('quantity', 'instrument')
            ->toArray();
    }
}
