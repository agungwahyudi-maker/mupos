<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\KasirController;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\MidtransWebhookController;

Route::post('/checkout', [CheckoutController::class, 'store']);

Route::post('/midtrans/webhook', [MidtransWebhookController::class, 'handle']);