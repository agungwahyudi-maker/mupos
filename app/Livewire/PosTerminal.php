<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;


class PosTerminal extends Component
{
    public $search = '';
    public $category = 'Semua';
    public $cart = [];

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