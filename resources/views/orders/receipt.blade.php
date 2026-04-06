<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pesanan #{{ $order->order_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</head>
<body class="bg-gray-100 flex flex-col items-center justify-center min-h-screen p-4">

    <div id="receipt-content" class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-sm text-center border border-gray-100">
        <h3 class="text-2xl font-black text-gray-800 mb-1">BUKTI PEMESANAN</h3>
        <p class="text-gray-500 text-sm mb-4">No: {{ $order->order_number }}</p>
        
        <div class="border-t border-b border-dashed py-4 my-4">
            <div class="flex justify-between text-left mb-2">
                <span class="text-gray-400">Pelanggan:</span>
                <span class="font-bold">{{ $order->customer_name }}</span>
            </div>
            <div class="flex justify-between text-left mb-2">
                <span class="text-gray-400">Meja:</span>
                <span class="font-bold">{{ $order->table_number }}</span>
            </div>
        </div>

        <div class="bg-gray-50 p-4 rounded-xl flex flex-col items-center mb-6">
            <div class="mb-2">
                {!! QrCode::size(80)->generate($order->order_number) !!}
            </div>
            <p class="text-xs font-mono tracking-widest text-gray-600">{{ $order->order_number }}</p>
        </div>
        
        <p class="text-sm text-gray-600 leading-relaxed mb-6">
            Silakan tunjukkan barcode ini ke <span class="font-bold text-orange-600">Kasir</span> untuk melakukan pembayaran dan memproses pesanan.
        </p>
    </div>

    <div class="mt-6 w-full max-w-sm space-y-3">
        <button onclick="downloadReceipt()" class="w-full bg-orange-500 text-white font-bold py-4 rounded-2xl shadow-lg active:scale-95 transition">
            Simpan Sebagai Gambar
        </button>
        <a href="/" class="block text-center text-gray-400 font-medium text-sm">Kembali ke Menu</a>
    </div>

    <script>
        function downloadReceipt() {
            const receipt = document.querySelector("#receipt-content");
            html2canvas(receipt, {
                backgroundColor: "#ffffff",
                scale: 3 // Biar hasil gambarnya tajam (HD)
            }).then(canvas => {
                let link = document.createElement('a');
                link.download = 'Struk-{{ $order->order_number }}.png';
                link.href = canvas.toDataURL("image/png");
                link.click();
            });
        }
    </script>
</body>
</html>