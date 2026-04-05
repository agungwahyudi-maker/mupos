<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Struk #{{ $order->id }}</title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; font-size: 12px; line-height: 1.2; }
        .ticket { width: 58mm; max-width: 58mm; }
        .center { text-align: center; }
        .right { text-align: right; }
        table { width: 100%; border-collapse: collapse; }
        .dashed-line { border-top: 1px dashed #000; margin: 5px 0; }
        
        /* CSS khusus untuk printer */
        @media print {
            @page { margin: 0; }
            body { margin: 0; padding: 5px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print(); window.onafterprint = function(){ window.history.back(); }">
    <div class="ticket">
        <div class="center">
            <strong>Marem Rasa Kafe & Resto</strong><br>
            Alamat Toko, Kota<br>
            Telp: 08123456789
        </div>
        
        <div class="dashed-line"></div>
        
        <div>
            Tgl: {{ $order->created_at->format('d/m/Y H:i') }}<br>
            No : #{{ $order->id }}<br>
            Plgn: {{ $order->customer_name ?? 'Guest' }} (Meja: {{ $order->table_number }})
            Pembayaran: 
            @if($order->payment_method === 'cash')
                CASH
            @else
                QRIS/Transfer Bank
            @endif
            <br>
        </div>
        
        <div class="dashed-line"></div>
         
        <table>
            @foreach($order->items as $item)
            <tr>
                <td colspan="2">{{ $item->product->name }}</td>
            </tr>
            <tr>
                <td>{{ $item->quantity }} x {{ number_format($item->price_at_sale) }}</td>
                <td class="right">{{ number_format($item->quantity * $item->price_at_sale) }}</td>
            </tr>
            @endforeach
        </table>
        
        <div class="dashed-line"></div>
        
        <table>
            <tr>
                <td><strong>TOTAL</strong></td>
                <td class="right"><strong>{{ number_format($order->total_price) }}</strong></td>
            </tr>
            @if($order->payment_method === 'cash')
            <tr>
                <td>Bayar</td>
                <td class="right">{{ number_format($order->bayar) }}</td>
            </tr>
            <tr>
                <td>Kembali</td>
                <td class="right">{{ number_format($order->kembalian) }}</td>
            </tr>
            @else
            <tr>
                <td>Metode</td>
                <td class="right">QRIS/Transfer Bank</td>
            </tr>
            <tr>
                <td>Status</td>
                <td class="right">LUNAS</td>
            </tr>
            @endif
        </table>
        
        <div class="dashed-line"></div>

        <div class="center">
            <small>Scan Struk</small><br>

            {!! QrCode::size(80)->generate(json_encode([
                'invoice' => $order->order_number,
                'total' => $order->total_price,
                'payment' => $order->payment_method,
                'items' => $order->items->map(fn($i) => [
                    'name' => $i->product->name,
                    'qty' => $i->quantity
                ])
            ])) !!}
        </div>
        
        <div class="center">
            Terima Kasih<br>
            Silahkan Datang Kembali
        </div>
    </div>

    <div class="no-print" style="margin-top: 20px;">
        <button onclick="window.print()">Cetak Ulang</button>
        <button onclick="window.history.back()">Kembali</button>
    </div>
    <script>
        window.onload = function() {
            window.print();
            // Opsional: Tutup jendela atau kembali ke kasir setelah cetak
            setTimeout(function() {
                window.location.href = '/kasir'; 
            }, 1000);
        }
    </script>
</body>
</html>