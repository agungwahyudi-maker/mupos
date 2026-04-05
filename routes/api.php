<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\KasirController;
use Illuminate\Http\Request;

Route::post('/checkout', [CheckoutController::class, 'store']);