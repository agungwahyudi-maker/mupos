<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $data = $request->all();

        \Log::info('MIDTRANS:', $data);

        $order = Order::where('order_number', $data['order_id'])->first();

        if (!$order) {
            return response()->json(['message' => 'Order tidak ditemukan'], 404);
        }

        $status = $data['transaction_status'];

        if ($status == 'settlement') {
            //update bayar dan kembalian jika status settlement
            $order->bayar = $order->total_price; // Karena ini pembayaran online, anggap semua dibayar
            $order->kembalian = 0; // Karena ini pembayaran online
            $order->status = 'paid';
        } elseif ($status == 'pending') {
            $order->status = 'pending';
        } elseif ($status == 'expire') {
            $order->status = 'expired';
        } elseif ($status == 'cancel') {
            $order->status = 'cancelled';
        }

        $order->save();

        return response()->json(['success' => true]);
    }
}
