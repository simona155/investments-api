<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Models\Account;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    public function store(
        StoreTransactionRequest $request,
        TransactionService $transactionService
    ): JsonResponse {
        $data = $request->validated();

        $account = Account::findOrFail($data['account_id']);

        if ($data['type'] === 'deposit') {
            $transaction = $transactionService->deposit(
                $account,
                $data['amount']
            );

            return response()->json($transaction, 201);
        }

        if ($data['type'] === 'withdrawal') {
            $transaction = $transactionService->withdraw(
                $account,
                $data['amount']
            );

            return response()->json($transaction, 201);
        }

        return response()->json([
            'message' => 'This transaction type is not implemented yet.'
        ], 422);
    }
}
