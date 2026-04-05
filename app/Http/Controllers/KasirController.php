<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class KasirController extends Controller
{
    public function index()
    {
        $products = Product::all()->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'price' => $p->price,
                'category' => $p->category,
                'image' => $p->image 
                    ? asset('storage/' . $p->image)
                    : null,
            ];
        });

        // dd($products);
        $categories = $products->pluck('category')->unique()->values();
        return view('kasir.index', compact('products', 'categories'));
    }
    
    public function checkout(Request $request) {
        try {
            // dd($request->all());
            return DB::transaction(function () use ($request) {
                $order = Order::create([
                    'order_number'  => 'INV-' . now()->format('YmdHis'),
                    'customer_name' => $request->customer_name,
                    'total_price'   => $request->total_price, // Sesuaikan nama
                    'status'        => 'success',
                    'bayar'         => $request->cash, // Sesuaikan nama
                    'user_id'       => auth()->id() ?? 1, 
                ]);

                // Looping items dari payload bersih tadi
                foreach ($request->items as $item) {
                    OrderItem::create([
                        'order_id'      => $order->id,
                        'product_id'    => $item['id'],
                        'quantity'      => $item['qty'],
                        'price_at_sale' => $item['price'],
                    ]);
                }

                return response()->json(['id' => $order->id, 'success' => true]);
            });
        } catch (\Exception $e) {
            // Ini akan mengirimkan pesan error asli ke console browser (bukan lagi <DOCTYPE... )
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
