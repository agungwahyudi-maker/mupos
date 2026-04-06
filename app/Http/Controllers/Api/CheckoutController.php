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
    // public function store(Request $request)
    // {
    //     try {
    //         // Gunakan Transaction agar data tidak setengah tersimpan jika error
    //         return DB::transaction(function () use ($request) {
                
    //             // 1. Simpan Data Order
    //             $order = Order::create([
    //                 'shop_id'        => 1, // Sesuaikan jika ada multi-toko
    //                 'customer_name'  => $request->customer_name,
    //                 'table_number'   => $request->table_number,
    //                 'total_price'    => $request->total_price,
    //                 'payment_method' => $request->payment_method,
    //                 'user_id'       => auth()->id() ?? 1, // Pastikan user_id valid
    //                 'bayar'         => $request->cash, // Pastikan kolom status ada di DB
    //                 'kembalian'     => $request->kembalian, // Hitung kembalian
    //                 'order_number'   => 'INV-' . time(), // Sebaiknya ada nomor invoice
    //             ]);

    //             // 2. Simpan Item Pesanan (Looping dari cart)
    //             $items=$request->items ?? [];
    //             foreach ($items as $item) {
    //                 OrderItem::create([
    //                     'order_id'      => $order->id,
    //                     'product_id'    => $item['id'],
    //                     'quantity'      => $item['qty'],
    //                     'price_at_sale' => $item['price'],
    //                 ]);
    //             }

    //             //jika cash, langsung simpan dan kirim respon sukses
    //             if($request->payment_method === 'cash') {
    //                 return response()->json([
    //                     'success' => true,
    //                     'id'      => $order->id,
    //                     'message' => 'Pesanan berhasil disimpan'
    //                 ]);
    //             }

                

    //             // 3. Kirim respon JSON (Bukan dd!)
    //             return response()->json([
    //                 'success' => true,
    //                 'id'      => $order->id,
    //                 'message' => 'Pesanan berhasil disimpan'
    //             ]);
    //         });

    //     } catch (\Exception $e) {
    //         // Jika gagal, kirim pesan error yang bisa dibaca JS
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    // public function store(Request $request)
    // {
    //     try {
    //         return DB::transaction(function () use ($request) {


    //             //hasilscan jika ada order_number update data, jika tidak buat baru
    //             if ($request->order_id) {
    //                 $order = Order::find($request->order_id);

    //                 // 🔥 HAPUS ITEM LAMA
    //                 $order->items()->delete();

    //                 // 🔥 INSERT ULANG
    //                 foreach ($request->items as $item) {
    //                     OrderItem::create([
    //                         'order_id' => $order->id,
    //                         'product_id' => $item['id'],
    //                         'quantity' => $item['qty'],
    //                         'price_at_sale' => $item['price'],
    //                     ]);
    //                 }

    //                 // update total
    //                 $order->update([
    //                     'total_price' => $request->total_price
    //                 ]);
    //             }




                
    //             // 1. Simpan Order
    //             $order = Order::create([
    //                 'shop_id'        => 1,
    //                 'customer_name'  => $request->customer_name,
    //                 'table_number'   => $request->table_number,
    //                 'total_price'    => $request->total_price,
    //                 'payment_method' => $request->payment_method,
    //                 'user_id'        => auth()->id() ?? 1,
    //                 'bayar'          => $request->cash,
    //                 'kembalian'      => $request->kembalian,
    //                 'order_number'   => 'INV-' . time(),
    //             ]);

    //             // 2. Simpan Item
    //             foreach ($request->items ?? [] as $item) {
    //                 OrderItem::create([
    //                     'order_id'      => $order->id,
    //                     'product_id'    => $item['id'],
    //                     'quantity'      => $item['qty'],
    //                     'price_at_sale' => $item['price'],
    //                 ]);
    //             }

    //             // ✅ CASH
    //             if ($request->payment_method === 'cash') {
    //                 return response()->json([
    //                     'success' => true,
    //                     'type'    => 'cash',
    //                     'id'      => $order->id
    //                 ]);
    //             }

    //             // =========================
    //             // 🔥 MIDTRANS START DI SINI
    //             // =========================

    //             if ($request->payment_method === 'midtrans') {

    //                 Config::$serverKey = config('midtrans.server_key');
    //                 Config::$isProduction = false;
    //                 Config::$isSanitized = true;
    //                 Config::$is3ds = true;

    //                 $params = [
    //                     'transaction_details' => [
    //                         'order_id' => $order->order_number, // gunakan invoice
    //                         'gross_amount' => (int) $order->total_price,
    //                     ],
    //                     'customer_details' => [
    //                         'first_name' => $order->customer_name,
    //                     ],

    //                     // TAMBAHKAN INI: Memaksa hanya QRIS yang muncul
    //                 'enabled_payments' => ['gopay', 'shopeepay', 'other_qris'],
    //                 ];

    //                 $snapToken = Snap::getSnapToken($params);

    //                 return response()->json([
    //                     'success'    => true,
    //                     'type'       => 'midtrans',
    //                     'snap_token' => $snapToken,
    //                     'order_id'   => $order->id
    //                 ]);
    //             }
                

    //         });

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function store(Request $request)
    {
        try {
            return DB::transaction(function () use ($request) {

                // 🔥 CEK: JIKA ADA ORDER_ID → UPDATE
                if ($request->order_id) {

                    $order = Order::findOrFail($request->order_id);

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
                        'bayar'          => $request->cash ?? 0,
                        'kembalian'      => $request->kembalian ?? 0,
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
                        'bayar'          => $request->cash,
                        'kembalian'      => $request->kembalian,
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