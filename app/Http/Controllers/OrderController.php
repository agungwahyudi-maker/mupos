<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function print(Order $order) {
        return view('print.receipt', compact('order'));
    }

    public function scan($code)
    {
        $order = Order::with('items.product')
            ->where('order_number', $code)
            ->first();

        if (!$order) {
            return response()->json([
                'status' => 'not_found'
            ]);
        }

        return response()->json([
            'status' => $order->status,
            'order_id' => $order->id,
            'items' => $order->items->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'name' => $item->product->name,
                    'price' => $item->price_at_sale,
                    'qty' => $item->quantity
                ];
            })
        ]);
        
    }
}
