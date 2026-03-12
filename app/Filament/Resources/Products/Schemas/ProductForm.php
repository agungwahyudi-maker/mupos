<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Toggle;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Tambahkan komponen form di sini, misalnya:
                TextInput::make('name')->label('Nama Produk')->required(),
                TextInput::make('price')->label('Harga')->numeric()->required(),
                Hidden::make('shop_id')
                    ->default(auth()->user()->shop_id ?? 1)
                    ->required(),
                Select::make('category')->label('Kategori')->options([
                    'makanan' => 'Makanan',
                    'minuman' => 'Minuman',
                    'snack' => 'Snack',
                    'lainnya' => 'Lainnya',
                ])->required(),
                Toggle::make('is_active')
                    ->label('Status Produk')
                    ->helperText('Hijau = Aktif (1), Merah = Off (2)')
                    // 1. Membaca dari Database: Jika nilainya 1, toggle akan otomatis ON (Hijau)
    ->formatStateUsing(function ($state) {
        return $state === 1; // Mengembalikan true jika 1, false jika 2
    })
                    // Sebelum simpan ke database, ubah true menjadi 1, false menjadi 2
                    ->dehydrateStateUsing(fn ($state) => $state ? 1 : 2)
                    ->onColor('success')
                    ->offColor('danger')
                    ->required(),
                FileUpload::make('image')
                    ->label('Gambar Produk')
                    ->image()
                    ->directory('product-images')
                    ->visibility('public')
                    ->maxSize(1024) // Maksimal 1MB
                    ->required(),
            ]);
    }
}
