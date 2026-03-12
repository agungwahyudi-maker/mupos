<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plan;
use App\Models\Shop;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;

class MuposSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat Paket Langganan (Plans)
        $planHemat = Plan::create([
            'name' => 'Paket Hemat',
            'price' => 50000,
            'max_employees' => 2,
            'features' => ['pos_basic', 'report_daily'],
        ]);

        $planPro = Plan::create([
            'name' => 'Paket Pro',
            'price' => 150000,
            'max_employees' => 10,
            'features' => ['pos_pro', 'inventory', 'report_monthly', 'multi_device'],
        ]);

        // 2. Buat Toko Contoh (Shop)
        $shop = Shop::create([
            'name' => 'Resto Sedap Malam',
            'slug' => 'resto-sedap-malam',
            'plan_id' => $planPro->id,
            'is_active' => true,
            'expires_at' => now()->addYear(),
        ]);

        // 3. Buat User Owner untuk Toko Tersebut
        User::create([
            'shop_id' => $shop->id,
            'name' => 'Owner Resto',
            'email' => 'owner@mupos.com',
            'password' => Hash::make('password'),
            'role' => 'owner',
        ]);

        // 4. Buat Produk Contoh (Menu)
        $menus = [
            ['name' => 'Nasi Goreng Jawa', 'cat' => 'makanan', 'price' => 20000],
            ['name' => 'Ayam Bakar Madu', 'cat' => 'makanan', 'price' => 25000],
            ['name' => 'Es Teh Manis', 'cat' => 'minuman', 'price' => 5000],
            ['name' => 'Kopi Susu Gula Aren', 'cat' => 'minuman', 'price' => 15000],
            ['name' => 'Kentang Goreng', 'cat' => 'snack', 'price' => 12000],
            ['name' => 'Cireng Crispy', 'cat' => 'snack', 'price' => 10000],
            ['name' => 'Kerupuk Kaleng', 'cat' => 'lain-lain', 'price' => 2000],
        ];

        foreach ($menus as $menu) {
            Product::create([
                'shop_id' => $shop->id,
                'name' => $menu['name'],
                'category' => $menu['cat'],
                'price' => $menu['price'],
                'stock' => 50,
                'is_active' => true,
            ]);
        }
    }
}
