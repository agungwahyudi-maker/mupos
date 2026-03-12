
<div>
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
                <div wire:click="addToCart({{ $product->id }})" class="bg-white p-3 rounded-2xl border border-gray-100 hover:border-orange-500 transition-all cursor-pointer group">
                    <div class="aspect-square bg-gray-100 rounded-xl mb-3 overflow-hidden">
                        <img src="{{ $product->image ? asset('storage/'.$product->image) : 'https://via.placeholder.com/200' }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                    </div>
                    <h3 class="font-bold text-gray-800 truncate">{{ $product->name }}</h3>
                    <p class="text-sm text-gray-500 mb-2 capitalize">{{ $product->category }}</p>
                    <div class="flex justify-between items-center">
                        <span class="font-black text-orange-600 text-lg">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        <div class="bg-orange-100 text-orange-600 p-2 rounded-lg group-hover:bg-orange-500 group-hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="w-full lg:w-[400px]">
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden flex flex-col h-[calc(100vh-60px)] sticky top-6 border border-gray-100">
                <div class="p-5 border-b flex justify-between items-center">
                    <h2 class="text-xl font-black text-gray-800 tracking-tight">PESANAN</h2>
                </div>

                <div class="flex-1 overflow-y-auto p-5 custom-scroll space-y-4">
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
                    <button class="w-full bg-orange-500 hover:bg-orange-600 text-white py-4 rounded-2xl font-black text-lg shadow-lg shadow-orange-200 transition transform active:scale-95">
                        KONFIRMASI PESANAN
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>