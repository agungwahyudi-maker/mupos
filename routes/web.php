<?php
use App\Livewire\PosTerminal;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\PosTerminalController;
use App\Http\Controllers\OrderController;

Route::get('/', PosTerminal::class);
Route::get('/order/{order}/print', [OrderController::class, 'print'])->name('order.print');
