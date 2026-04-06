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
//print dari pengunjung
Route::get('/order/receipt/{order_number}', function ($order_number) {
    // Cari order berdasarkan order_number (INV-xxxx)
    $order = Order::where('order_number', $order_number)->firstOrFail();
    // dd($order_number); // Debugging: pastikan order ditemukan berdasarkan order_number
    return view('orders.receipt', compact('order'));
})->name('order.receipt');

Route::get('/order/scan/{code}', [OrderController::class, 'scan']);
