<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $order->order_number }}</title>
    <style>
        /* CSS yang Anda berikan tetap sama */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Courier New', monospace; width: 58mm; padding: 0; }
        .receipt { width: 100%; padding: 8px; font-size: 12px; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 8px; border-bottom: 1px dashed #000; padding-bottom: 8px; }
        .restaurant-name { font-weight: bold; font-size: 14px; text-transform: uppercase; }
        .restaurant-info { font-size: 10px; }
        .transaction-info { font-size: 10px; margin-bottom: 8px; }
        .info-row { display: flex; justify-content: space-between; }
        .items { margin: 8px 0; border-bottom: 1px dashed #000; padding-bottom: 8px; }
        .item-row { display: flex; justify-content: space-between; margin-bottom: 4px; font-size: 11px; }
        .item-name { flex: 1; }
        .item-qty { width: 30px; text-align: center; }
        .item-price { width: 60px; text-align: right; }
        .totals { margin: 8px 0; border-bottom: 1px dashed #000; padding-bottom: 8px; }
        .total-row { display: flex; justify-content: space-between; font-size: 11px; }
        .grand-total { font-weight: bold; font-size: 13px; margin-top: 4px; border-top: 1px solid #000; padding-top: 4px;}
        .daftar-pesanan { font-weight: bold; font-size: 12px; text-align: center; margin-bottom: 8px; }
        .footer { text-align: center; font-size: 10px; padding-top: 8px; }
        
        @media print {
            @page { margin: 0; }
            body { width: 58mm; height: auto; margin: 0; padding: 0; }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <div style="margin-bottom: 1px;">
                <img src="{{ asset('images/logos.png') }}" style="width: 100px; height: auto;">
            </div>
            <div class="restaurant-name">Marem Rasa Cafe & Resto</div>
            <div class="restaurant-info">
                <div>Lantai 1, Gedung IT</div>
                <div>(021) 8888-9999</div>
            </div>
        </div>

        <div class="transaction-info">
            <div class="info-row">
                <span>Invoice:</span>
                <span>#{{ $order->order_number }}</span>
            </div>
            <div class="info-row">
                <span>Pelanggan:</span>
                <span>{{ $order->customer_name ?? 'Umum' }}</span>
            </div>
            <div class="info-row">
                <span>Meja:</span>
                <span>{{ $order->table_number ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span>Tanggal:</span>
                <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>
        
        <hr>
        <div class="items">
            <center class="daftar-pesanan">Daftar Pesanan</center>
            @foreach($order->items as $item)
            <div class="item-row">
                <span>{{$loop->iteration}}.</span>
                <span class="item-name">{{ $item->product->name }}</span>
                <span class="item-qty">{{ $juml=$item->quantity }} x {{ $harga=number_format($item->price_at_sale, 0, ',', '.') }}</span>
                <span class="item-price">{{ $subtotal=number_format($juml * $item->price_at_sale, 0, ',', '.') }}</span>
            </div>
            @endforeach
        </div>

        <div class="totals">
            <div class="total-row grand-total">
                <span>TOTAL</span>
                <span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="footer">
            <div style="font-weight: bold">Terima Kasih!</div>
            <div>Silahkan datang kembali</div>
            <div style="margin-top: 8px; font-size: 8px;">{{ENV('APP_NAME')}} {{ now()->format('H:i:s') }}</div>
        </div>
    </div>

    <script>
        window.print();
        // Menutup tab otomatis setelah print/cancel (opsional)
        window.onafterprint = function() {
            window.close();
        };
    </script>
</body>
</html>