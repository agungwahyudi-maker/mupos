<?php
use App\Livewire\PosTerminal;
use Illuminate\Support\Facades\Route;
use App\Models\Order;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\KasirController;

Route::get('/', PosTerminal::class);
Route::get('/order/{order}/print', [OrderController::class, 'print'])->name('order.print');
// routes/web.php
Route::get('/kasir', [KasirController::class, 'index'])->name('kasir');
// routes/api.php
Route::post('/checkout', [KasirController::class, 'checkout']);

// routes/web.php
Route::get('/print/{id}', function ($id) {
    // Gunakan 'items.product' bukan 'orderItems.product'
    $order = Order::with('items.product')->findOrFail($id); 
    return view('pos.print', compact('order'));
});
