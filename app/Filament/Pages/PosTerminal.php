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

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;
    public function getHeader(): ?\Illuminate\Contracts\View\View
    {
        // Menghilangkan header default agar area kerja lebih luas
        return null;
    }



    public function getMaxContentWidth(): Width
    {
        return Width::Full;
    }
}
