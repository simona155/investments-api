<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Services\AccountBalanceService;
use Illuminate\Http\JsonResponse;

class AccountController extends Controller
{
    public function balance(
        Account $account,
        AccountBalanceService $accountBalanceService
    ): JsonResponse {
        return response()->json([
            'balance' => $accountBalanceService->getBalance($account),
        ]);
    }

    public function holdings(
        Account $account,
        AccountBalanceService $accountBalanceService
    ): JsonResponse {
        return response()->json([
            'holdings' => $accountBalanceService->getHoldings($account),
        ]);
    }
}
