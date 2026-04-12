<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB; // Tambahkan ini
use Midtrans\Config;
use Midtrans\Snap;

class CheckoutController extends Controller
{

    public function store(Request $request)
    {
        try {
            return DB::transaction(function () use ($request) {

                // 🔥 CEK: JIKA ADA ORDER_ID → UPDATE
                if ($request->order_id || $request->order_number) {

                    $order = Order::where('id', $request->order_id)->orWhere('order_number', $request->order_number)->firstOrFail();
                    //cek isi request dan order

                    // dd($request->all(), $order->toArray());
                    // Hapus item lama
                    $order->items()->delete();

                    // Insert ulang item
                    foreach ($request->items as $item) {
                        OrderItem::create([
                            'order_id'      => $order->id,
                            'product_id'    => $item['id'],
                            'quantity'      => $item['qty'],
                            'price_at_sale' => $item['price'],
                        ]);
                    }

                    // Update order
                    $order->update([
                        'total_price'    => $request->total_price,
                        'payment_method' => $request->payment_method,
                        'bayar'          => $request->bayar ?? 0,
                        'kembalian'      => $request->kembalian ?? 0,
                        'status'          => 'paid',
                    ]);

                } else {
                    // 🔥 JIKA TIDAK ADA → CREATE BARU
                    $order = Order::create([
                        'shop_id'        => 1,
                        'customer_name'  => $request->customer_name,
                        'table_number'   => $request->table_number,
                        'total_price'    => $request->total_price,
                        'payment_method' => $request->payment_method,
                        'user_id'        => auth()->id() ?? 1,
                        'bayar'          => $request->bayar ?? 0,
                        'kembalian'      => $request->kembalian ?? 0,
                        'status'          => 'paid',
                        'order_number'   => 'INV-' . time(),
                    ]);

                    foreach ($request->items ?? [] as $item) {
                        OrderItem::create([
                            'order_id'      => $order->id,
                            'product_id'    => $item['id'],
                            'quantity'      => $item['qty'],
                            'price_at_sale' => $item['price'],
                        ]);
                    }
                }

                // =====================
                // 💵 CASH
                // =====================
                if ($request->payment_method === 'cash') {
                    return response()->json([
                        'success' => true,
                        'type'    => 'cash',
                        'id'      => $order->id
                    ]);
                }

                // =====================
                // 💳 MIDTRANS
                // =====================
                if ($request->payment_method === 'midtrans') {

                    Config::$serverKey = config('midtrans.server_key');
                    Config::$isProduction = false;
                    Config::$isSanitized = true;
                    Config::$is3ds = true;

                    $params = [
                        'transaction_details' => [
                            'order_id'     => $order->order_number,
                            'gross_amount' => (int) $order->total_price,
                        ],
                        'customer_details' => [
                            'first_name' => $order->customer_name,
                        ],
                        'enabled_payments' => ['gopay', 'shopeepay', 'other_qris'],
                    ];

                    $snapToken = Snap::getSnapToken($params);

                    return response()->json([
                        'success'    => true,
                        'type'       => 'midtrans',
                        'snap_token' => $snapToken,
                        'order_id'   => $order->id
                    ]);
                }

            });

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}