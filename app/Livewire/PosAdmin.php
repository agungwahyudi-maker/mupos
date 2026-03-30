<?php
namespace App\Livewire;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class PosAdmin extends Component
{
    public $search = '';
    public $category = 'Semua';
    public $cart = [];
    // 1. Tambahkan properti ini agar Livewire bisa menangkap inputan
    public $customerName = ''; 
    public $nomormeja = '';


    public function addToCart($productId)
    {
        $product = Product::find($productId);
        
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['qty']++;
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'qty' => 1,
            ];
        }
    }

    public function updateQty($productId, $qty)
    {
        if ($qty <= 0) {
            unset($this->cart[$productId]);
        } else {
            $this->cart[$productId]['qty'] = $qty;
        }
    }

    public function checkout()
    {
        if (empty($this->cart)) return;

        $orderId = DB::transaction(function () {
            $order = Order::create([
                'order_number' => 'INV-' . now()->format('YmdHis'),
                'customer_name' => $this->customer_name,
                'table_number' => $this->table_number,
                'total_price' => collect($this->cart)->sum(fn($item) => $item['price'] * $item['qty']),
                'status' => 'success',
                'user_id' => auth()->id(), // Mencatat kasir yang sedang login
            ]);

            foreach ($this->cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['qty'],
                    'price_at_sale' => $item['price'],
                ]);
            }
            return $order->id;
        });

        // Reset keranjang
        $this->reset(['cart', 'customer_name', 'table_number']);

        // Redirect ke route cetak yang kita buat sebelumnya di tab baru
        return redirect()->route('order.print', $orderId);
    }

    public function render()
    {
        $products = Product::where('name', 'like', '%' . $this->search . '%')->get();
        return view('livewire.pos-admin', compact('products'));
        
    }
}