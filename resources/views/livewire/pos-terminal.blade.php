<div>
    <div> @if (session()->has('success'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 5000)"
             class="fixed top-5 right-5 z-[99] w-80 animate-fade-in-down">
            
            <div class="bg-white border-l-4 border-green-500 shadow-2xl rounded-xl p-4 flex items-start gap-4">
                <div class="bg-green-100 p-2 rounded-full text-green-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-gray-800">{{ session('success') }}</h3>
                    <div class="text-sm text-gray-600 mt-1">
                        <p>Pelanggan: <span class="font-bold text-gray-900">{{ session('customer') ?? '-' }}</span></p>
                        <p>Meja: <span class="font-bold text-gray-900">{{ session('table') ?? '-' }}</span></p>
                    </div>
                </div>
                <button @click="show = false" class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
    @endif

    </div>

    <div class="max-w-[1600px] mx-auto p-4 lg:p-6">
    <div class="flex flex-col lg:flex-row gap-6">
        <div class="flex-1">
            <div class="mb-6 flex flex-col md:flex-row gap-4 justify-between items-center bg-white p-4 rounded-2xl shadow-sm">
                <div class="flex gap-2 overflow-x-auto pb-2 md:pb-0 w-full md:w-auto custom-scroll">
                    @foreach(['Semua', 'Makanan', 'Minuman', 'Snack', 'Lainnya'] as $cat)
                        <button 
                            wire:click="$set('category', '{{ $cat }}')"
                            class="px-5 py-2 rounded-xl font-medium transition {{ $category == $cat ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                            {{ $cat }}
                        </button>
                    @endforeach
                </div>
                <div class="relative w-full md:w-80">
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari menu..." class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($products as $product)
                    <div wire:click="addToCart({{ $product->id }})" 
                        wire:loading.attr="disabled"
                        wire:target="addToCart({{ $product->id }})"
                        class="bg-white p-3 rounded-2xl border border-gray-100 hover:border-orange-500 transition-all cursor-pointer group relative overflow-hidden">
                        
                        <div wire:loading wire:target="addToCart({{ $product->id }})" 
                            class="absolute inset-0 bg-white/60 backdrop-blur-[1px] z-20 flex items-center justify-center">
                            <div class="flex flex-col items-center">
                                <svg class="animate-spin h-8 w-8 text-orange-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>

                        <div class="aspect-square bg-gray-100 rounded-xl mb-3 overflow-hidden">
                            <img src="{{ $product->image ? asset('storage/'.$product->image) : 'https://via.placeholder.com/200' }}" 
                                class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                        </div>
                        
                        <h3 class="font-bold text-gray-800 truncate">{{ $product->name }}</h3>
                        <p class="text-sm text-gray-500 mb-2 capitalize">{{ $product->category }}</p>
                        
                        <div class="flex justify-between items-center">
                            <span class="font-black text-orange-600 text-lg">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            
                            <div class="bg-orange-100 text-orange-600 p-2 rounded-lg group-hover:bg-orange-500 group-hover:text-white transition-colors">
                                <svg wire:loading.remove wire:target="addToCart({{ $product->id }})" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                                </svg>
                                <svg wire:loading wire:target="addToCart({{ $product->id }})" class="animate-spin h-5 w-5" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="w-full lg:w-[400px]" id="pesanan-section">
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden flex flex-col h-[calc(100vh-60px)] sticky top-6 border border-gray-100">
                <div class="p-5 border-b flex justify-between items-center">
                    <h2 class="text-xl font-black text-gray-800 tracking-tight text-center">PESANAN</h2>
                </div>
                

                <div class="flex-1 overflow-y-auto p-5 custom-scroll space-y-4">
                    <div class="p-5 border-b">
                        <label for="customerName" class="block text-sm font-medium text-gray-700 mb-1">Nama Pemesan</label>
                        <input wire:model="customerName" type="text" id="customerName" required placeholder="Nama Pemesan" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <label for="nomormeja" class="block text-sm font-medium text-gray-700 mb-1 mt-3">Nomor Meja</label>
                        <input wire:model="nomormeja" type="text" id="nomormeja" required placeholder="Nomor Meja" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                    @forelse($cart as $id => $item)
                    <div class="flex gap-4 items-center animate-fade-in">
                        <div class="w-16 h-16 bg-gray-100 rounded-xl overflow-hidden flex-shrink-0">
                            <img src="{{ $item['image'] ? asset('storage/'.$item['image']) : 'https://via.placeholder.com/100' }}" class="object-cover h-full w-full">
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-gray-800 text-sm">{{ $item['name'] }}</h4>
                            <p class="text-xs text-orange-600 font-bold">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                            <div class="flex items-center gap-3 mt-2">
                                <button wire:click="updateQty({{ $id }}, -1)" class="w-7 h-7 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-100">-</button>
                                <span class="text-sm font-black">{{ $item['qty'] }}</span>
                                <button wire:click="updateQty({{ $id }}, 1)" class="w-7 h-7 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-100">+</button>
                            </div>
                        </div>
                        <button wire:click="removeFromCart({{ $id }})" class="text-gray-300 hover:text-red-500 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>
                    @empty
                    <div class="text-center py-10">
                        <p class="text-gray-400">Keranjang masih kosong</p>
                    </div>
                    @endforelse
                </div>

                <div class="p-6 bg-gray-50 border-t space-y-4">
                    <div class="space-y-2 text-sm font-medium text-gray-500">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span class="text-gray-800">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Pajak (10%)</span>
                            <span class="text-gray-800">Rp {{ number_format($tax, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="pt-4 border-t flex justify-between items-center">
                        <span class="text-lg font-bold text-gray-800">Total</span>
                        <span class="text-2xl font-black text-orange-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                        <label class="block text-xs font-bold text-orange-800 uppercase mb-3">Metode Pembayaran</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" wire:model="paymentMethod" value="cash" class="peer hidden" name="paymentMethod">
                                <div class="p-3 border-2 border-white bg-white rounded-xl text-center peer-checked:border-orange-500 peer-checked:bg-orange-100 transition">
                                    <span class="text-sm font-bold text-gray-700">Bayar Kasir</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" wire:model="paymentMethod" value="midtrans" class="peer hidden" name="paymentMethod">
                                <div class="p-3 border-2 border-white bg-white rounded-xl text-center peer-checked:border-orange-500 peer-checked:bg-orange-100 transition">
                                    <span class="text-sm font-bold text-gray-700">QRIS / Online</span>
                                </div>
                            </div>

                     <button 
                        wire:click="checkout" 
                        wire:loading.attr="disabled" 
                        class="w-full bg-orange-500 hover:bg-orange-600 text-white py-4 rounded-2xl font-black text-lg shadow-lg transition transform active:scale-95 disabled:opacity-75 disabled:cursor-not-allowed">
                        
                        <span wire:loading.remove wire:target="checkout">
                            Konfirmasi Pesanan
                        </span>

                        <span wire:loading wire:target="checkout" class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memproses...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="fixed bottom-6 right-6 lg:hidden z-50">
    <a href="#pesanan-section" class="relative bg-orange-500 text-white p-4 rounded-full shadow-2xl flex items-center justify-center transition transform active:scale-90">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>

        @if(count($cart) > 0)
            <span class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-black px-2 py-1 rounded-full border-2 border-white animate-bounce">
                {{ collect($cart)->sum('qty') }}
            </span>
        @endif
    </a>
</div>
</div>

