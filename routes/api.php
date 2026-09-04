<?php

use Illuminate\Support\Facades\Route;

Route::post('/transactions', [\App\Http\Controllers\TransactionController::class, 'store']);

Route::get('/accounts/{account}/transactions', [\App\Http\Controllers\TransactionController::class, 'history']);

Route::get('/accounts/{account}/balance', [\App\Http\Controllers\AccountController::class, 'balance']);

Route::get('/accounts/{account}/holdings', [\App\Http\Controllers\AccountController::class, 'holdings']);
