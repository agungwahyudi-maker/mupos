<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;


class PosTerminal extends Component
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
        if ($product) {
            if (isset($this->cart[$productId])) {
                $this->cart[$productId]['qty']++;
            } else {
                $this->cart[$productId] = [
                    'name' => $product->name,
                    'price' => $product->price,
                    'qty' => 1,
                    'image' => $product->image,
                ];
            }
        }
    }

    public function removeFromCart($productId)
    {
        if (isset($this->cart[$productId])) {
            unset($this->cart[$productId]);
        }
    }

    public function updateQty($productId, $change)
    {
        if (isset($this->cart[$productId])) {
            // Menambahkan quantity berdasarkan nilai $change (-1 atau 1)
            $this->cart[$productId]['qty'] += $change;

            // Jika quantity menjadi 0 atau kurang, hapus item dari keranjang
            if ($this->cart[$productId]['qty'] <= 0) {
                unset($this->cart[$productId]);
            }
        }
    }

    public function checkout()
    {
        if (empty($this->cart)) {
            return session()->flash('error', 'Keranjang masih kosong!');
        }

        try {
            DB::transaction(function () {
                $totalPrice = collect($this->cart)->sum(fn($item) => $item['price'] * $item['qty']);

                // 2. Simpan ke tabel orders
                $order = Order::create([
                    'shop_id'        => auth()->user()->shop_id ?? 1,
                    'user_id'        => auth()->id(),
                    'order_number'   => 'INV-' . now()->format('YmdHis'),
                    'total_price'    => $totalPrice,
                    'payment_method' => 'cash',
                    
                    // SESUAIKAN DENGAN NAMA KOLOM DI DATABASE ANDA SETELAH MIGRATE
                    'customer_name'  => $this->customerName, 
                    'table_number'   => $this->nomormeja,
                ]);

                // 3. Simpan Detail Item
                foreach ($this->cart as $id => $item) {
                    OrderItem::create([
                        'order_id'      => $order->id,
                        'product_id'    => $id,
                        'quantity'      => $item['qty'],
                        'price_at_sale' => $item['price'],
                    ]);
                }
            });

            // Simpan data ke session flash sebelum di-reset
            session()->flash('success', "Pesanan Berhasil Disimpan!");
            session()->flash('customer', $this->customerName);
            session()->flash('table', $this->nomormeja);

            // 4. Reset form setelah berhasil
            $this->reset(['cart', 'customerName', 'nomormeja']);
            session()->flash('success', 'Pesanan berhasil disimpan!');

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $query = Product::where('shop_id', 1);

        if ($this->category !== 'Semua') {
            $query->where('category', strtolower($this->category));
        }

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }
        

        return view('livewire.pos-terminal', [
            'products' => $query->get(),
            'subtotal' => collect($this->cart)->sum(fn($item) => $item['price'] * $item['qty']),
            'tax' => collect($this->cart)->sum(fn($item) => $item['price'] * $item['qty']) * 0.1,
            'total' => collect($this->cart)->sum(fn($item) => $item['price'] * $item['qty']) * 1.1,
        ])->layout('layouts.app'); // Perhatikan: di Laravel 12 defaultnya sering di layouts.app
    }
}