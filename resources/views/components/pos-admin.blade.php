<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12 lg:col-span-8">
        <div class="mb-4">
            <input type="text" wire:model.live="search" 
                placeholder="Cari menu makanan atau minuman..." 
                class="w-full rounded-lg border-gray-300 dark:bg-gray-800 dark:border-gray-700 shadow-sm focus:ring-primary-500">
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            @foreach($products as $product)
            <button wire:click="addToCart({{ $product->id }})" 
                class="flex flex-col items-start p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm hover:border-primary-500 hover:ring-1 hover:ring-primary-500 transition-all text-left">
                <span class="font-bold text-gray-900 dark:text-white">{{ $product->name }}</span>
                <span class="text-primary-600 font-medium">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
            </button>
            @endforeach
        </div>
    </div>

    <div class="col-span-12 lg:col-span-4">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 sticky top-4">
            <h3 class="text-xl font-bold mb-4 flex items-center gap-2">
                <x-heroicon-o-shopping-cart class="w-6 h-6 text-primary-500" />
                Pesanan Baru
            </h3>

            <div class="space-y-3 mb-6">
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase">Nama Pelanggan</label>
                    <input type="text" wire:model="customer_name" class="w-full mt-1 rounded-md border-gray-300 dark:bg-gray-700">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase">Nomor Meja</label>
                    <input type="text" wire:model="table_number" class="w-full mt-1 rounded-md border-gray-300 dark:bg-gray-700">
                </div>
            </div>

            <div class="divide-y divide-gray-100 dark:divide-gray-700 max-h-64 overflow-y-auto mb-4">
                @forelse($cart as $id => $item)
                <div class="py-3 flex justify-between items-start">
                    <div class="flex-1">
                        <p class="font-medium text-sm">{{ $item['name'] }}</p>
                        <p class="text-xs text-gray-500">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="number" value="{{ $item['qty'] }}" 
                            wire:change="updateQty({{ $id }}, $event.target.value)"
                            class="w-14 text-center p-1 border-gray-300 rounded text-sm">
                    </div>
                </div>
                @empty
                <div class="py-8 text-center text-gray-400">Keranjang masih kosong</div>
                @endforelse
            </div>

            @if(!empty($cart))
            <div class="border-t dark:border-gray-700 pt-4">
                <div class="flex justify-between items-center mb-6">
                    <span class="text-gray-500">Total Pembayaran</span>
                    <span class="text-2xl font-black text-primary-600">
                        Rp {{ number_format(collect($cart)->sum(fn($i) => $i['price'] * $i['qty']), 0, ',', '.') }}
                    </span>
                </div>
                <button wire:click="checkout" 
                    class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 rounded-xl shadow-lg shadow-primary-200 dark:shadow-none transition-all">
                    PROSES & CETAK STRUK
                </button>
            </div>
            @endif
        </div>
    </div>
</div>