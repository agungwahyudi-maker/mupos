<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Product;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Filament\Support\Enums\Width;

class PosTerminal extends Page
{
    protected string $view = 'filament.pages.pos-admin';
    protected static ?string $model = Product::class;
     protected static ?string $navigationLabel = 'Kasir';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;


    public function mount(): void
    {
        //cek apakah user sudah login
        if (!auth()->check()) {
            redirect()->route('admin/login');
        }

        redirect()->route('kasir');
    }
}
