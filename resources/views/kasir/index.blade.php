<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Kasir</title>

    @vite('resources/css/app.css')

    <!-- Alpine -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gray-100">

<div x-data="posApp()" class="flex h-screen overflow-hidden">
    <!-- 🔥 MENU -->
    <div class="w-2/3 p-4 overflow-y-auto">
        <!-- SEARCH -->
        <input 
            type="text"
            x-model="search"
            placeholder="Cari menu..."
            class="w-full mb-3 p-3 border rounded-xl shadow-sm focus:ring focus:ring-blue-200"
        />

        <!-- 📂 CATEGORY -->
        <div class="flex gap-2 mb-4 overflow-x-auto pb-2">
            <button 
                @click="activeCategory = 'all'"
                :class="activeCategory === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-200'"
                class="px-4 py-1 rounded-full text-sm"
            >
                Semua
            </button>
            {{-- <div x-data="posApp()"> --}}
            {{-- <pre>{{ json_encode($products, JSON_PRETTY_PRINT) }}</pre> --}}
            @foreach($categories as $cat)
                <button 
                    @click="activeCategory = '{{ $cat }}'"
                    :class="activeCategory === '{{ $cat }}' ? 'bg-blue-600 text-white' : 'bg-gray-200'"
                    class="px-4 py-1 rounded-full text-sm"
                >
                    {{ $cat }}
                </button>
            @endforeach
        </div>

        <!-- GRID -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">

            <template x-for="product in filteredProducts" :key="product.id">
                <div 
                    @click="addToCart(product)"
                    class="bg-white rounded-2xl shadow hover:shadow-xl cursor-pointer overflow-hidden transition group"
                >

                {{-- <div x-text="product.image"></div> --}}
                    <!-- IMAGE -->
                    <div class="h-28 bg-gray-100 flex items-center justify-center overflow-hidden">
                        <img 
                            :src="product.image || 'https://via.placeholder.com/150'"
                            class="object-cover w-full h-full"
                        />

                    </div>

                    <!-- INFO -->
                    <div class="p-3">
                        <div class="font-semibold text-sm" x-text="product.name"></div>
                        <div class="text-xs text-gray-500 mt-1" x-text="product.category?.name"></div>

                        <div class="mt-2 font-bold text-blue-600">
                            Rp <span x-text="format(product.price)"></span>
                        </div>
                    </div>
                </div>
            </template>

        </div>
    </div>

    <!-- 🧾 CART -->
    <div class="w-1/3 bg-white border-l p-4 flex flex-col">

        <h2 class="text-xl font-bold mb-4">🧾 Keranjang</h2>
        <div class="mb-5 space-y-2">
            <input 
                type="text"
                x-model="customer_name"
                placeholder="Nama Customer"
                class="w-full border rounded-lg p-2"
            />

            <input 
                type="text"
                x-model="table_number"
                placeholder="No Meja"
                class="w-full border rounded-lg p-2"
            />
        </div>
        <!-- ITEMS -->
        <div class="flex-1 overflow-y-auto space-y-3">

            <template x-for="(item, id) in cart" :key="id">
                <div class="flex justify-between items-center border-b pb-2">
                    <div>
                        <div class="font-semibold text-sm" x-text="item.name"></div>
                        <div class="text-xs text-gray-500">
                            Rp <span x-text="format(item.price)"></span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button @click="decrease(id)" class="px-2 bg-gray-200 rounded">-</button>
                        <span x-text="item.qty"></span>
                        <button @click="increase(id)" class="px-2 bg-gray-200 rounded">+</button>
                    </div>
                </div>
            </template>

            <div x-show="Object.keys(cart).length === 0" class="text-gray-400 text-sm text-center mt-10">
                Keranjang kosong
            </div>

        </div>

        <!-- TOTAL -->
        <div class="mt-4 border-t pt-4">

            <div class="flex justify-between text-lg font-bold">
                <span>Total</span>
                <span>Rp <span x-text="format(total)"></span></span>
            </div>

            <!-- PAYMENT -->
            <select x-model="payment" class="w-full mt-3 border rounded p-2">
                <option value="cash">Cash</option>
                <option value="midtrans">QRIS/Transfer Bank</option>
            </select>

            <!-- CASH -->
            <div x-show="payment === 'cash'" class="mt-3">
                <label for="cash" class="block text-sm font-medium text-gray-700">Uang Bayar</label>
                <input 
                    type="number"
                    min="0"
                    x-model.number="cash"
                    placeholder="Uang bayar"
                    class="w-full border rounded p-2"
                />

                <div class="text-right mt-2 text-sm">
                    Kembalian: Rp <span x-text="format(change)"></span>
                </div>
            </div>

            <div class="flex gap-2 mt-3">
                <button 
                    @click="resetCart()"
                    class="w-full mt-2 bg-gray-200 hover:bg-gray-300 py-2 rounded-lg text-sm"
                >
                    Reset Keranjang
                </button>
                <button 
                    @click="console.log('Checking out...');checkout()"
                    :disabled="!canCheckout"
                    :class="canCheckout 
                        ? 'bg-blue-600 hover:bg-blue-700' 
                        : 'bg-gray-300 cursor-not-allowed'"
                    class="w-full mt-4 text-white py-3 rounded-xl font-bold transition"
                >
                    BAYAR
                </button>
            </div>

        </div>
    </div>

</div>

<div x-show="qris_url" class="mt-4 p-4 border rounded bg-white text-center">
    <p class="text-sm font-bold mb-2">Silahkan Scan QRIS</p>
    
    <img :src="qris_url" alt="QRIS Code" class="mx-auto w-64 shadow-sm">
    
    <p class="text-xs text-gray-500 mt-2">Nominal: Rp <span x-text="format(total)"></span></p>
</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js"
    data-client-key="{{ config('midtrans.client_key') }}">
</script>

<script>

function posApp() {
    return {
        products: @json($products),
        cart: {},
        payment: 'cash',
        cash: 0,
        search: '',
        activeCategory: 'all',

        customer_name: '',
        table_number: '',

        get filteredProducts() {
            return this.products.filter(p => {
                let matchSearch = p.name.toLowerCase().includes(this.search.toLowerCase());

                let matchCategory = this.activeCategory === 'all' 
                    || p.category === this.activeCategory;

                return matchSearch && matchCategory;
            });
        },

        get total() {
            return Object.values(this.cart)
                .reduce((sum, item) => sum + (item.qty * item.price), 0);
        },

        get change() {
            return this.cash - this.total;
        },

        // ✅ VALIDASI CHECKOUT
        get canCheckout() {
            if (Object.keys(this.cart).length === 0) return false;
            if (!this.customer_name) return false;

            if (this.payment === 'cash') {
                return this.cash >= this.total;
            }

            return true;
        },

        addToCart(product) {
            if (!this.cart[product.id]) {
                this.cart[product.id] = {...product, qty: 1};
            } else {
                this.cart[product.id].qty++;
            }
        },

        increase(id) {
            this.cart[id].qty++;
        },

        decrease(id) {
            this.cart[id].qty--;
            if (this.cart[id].qty <= 0) delete this.cart[id];
        },

        resetCart() {
            this.cart = {};
            this.cash = 0;
        },

        // Di dalam data AlpineJS Anda
        data() {
            return {
                qris_url: null,
                // ...
            }
        },
        resetCartAll() {
            this.cart = {};
            this.cash = 0;
            this.customer_name = '';
            this.table_number = '';
        },

        format(value) {
            return new Intl.NumberFormat('id-ID').format(value);
        },


        async checkout() {
        if (!this.canCheckout) return;

        // Tampilkan loading agar user tidak klik berkali-kali
        Swal.fire({
            title: 'Memproses Pesanan...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading() }
        });

        const payload = {
            customer_name: this.customer_name,
            table_number: this.table_number,
            // Pastikan qty dan price adalah angka
            items: Object.values(this.cart).map(item => ({
                id: item.id,
                name: item.name,
                qty: parseInt(item.qty),
                price: parseInt(item.price) 
            })),
            total_price: parseInt(this.total), // <--- Paksa jadi Integer
            payment_method: this.payment,
            cash: parseInt(this.cash || 0)
        };
        console.log("Data yang dikirim ke server:", payload);
        
        try {
            let response = await fetch('/api/checkout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    customer_name: this.customer_name,
                    table_number: this.table_number,
                    items: Object.values(this.cart), // Gunakan 'items' sesuai Controller Anda
                    total_price: this.total,
                    payment_method: this.payment,
                    cash: this.cash,
                    kembalian: (this.cash - this.total)
                })
            });

            let result = await response.json();

            if (response.ok && result.success) {

                console.log("Response dari server:", result);
                if(result.type==='cash'){
                    this.resetCart();
                    // NOTIFIKASI BERHASIL
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Transaksi Cash telah disimpan.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Redirect ke halaman print setelah notifikasi hilang
                        window.location.href = '/print/' + result.id;
                    });
                }

                if (result.type === 'midtrans') {

                    let orderId = result.order_id; // ✅ ambil dari backend
                    let snapToken = result.snap_token;
                    let self = this; // ✅ simpan context Alpine
                    if (result.qris_url) {
                        this.qris_url = result.qris_url; // Simpan URL-nya
                    }
                    snap.pay(snapToken, {

                        onSuccess: function(res) {
                            console.log('Pembayaran berhasil:', res);

                            Swal.fire({
                                icon: 'success',
                                title: 'Pembayaran Berhasil',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = '/print/' + orderId;
                            });

                            self.resetCartAll(); // ✅ FIX
                        },

                        onPending: function(res) {
                            console.log('Pending:', res);

                            Swal.fire({
                                icon: 'info',
                                title: 'Menunggu Pembayaran'
                            });
                        },

                        onError: function(res) {
                            console.log('Error:', res);

                            Swal.fire({
                                icon: 'error',
                                title: 'Pembayaran Gagal'
                            });
                        }
                    });
                }
                
            } else {
                // NOTIFIKASI GAGAL DARI SERVER (Misal: Stok Habis/Database Error)
                Swal.fire({
                    icon: 'error',
                    title: 'Transaksi Gagal',
                    text: result.message || 'Terjadi kesalahan pada server.'
                });
            }

        } catch (error) {
            // NOTIFIKASI GAGAL KONEKSI
            console.log(error);
        }
    }
    }
}
</script>

</body>
</html>