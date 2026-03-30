<?php

namespace App\Filament\Resources\Orders;

use App\Filament\Resources\Orders\Pages\CreateOrder;
use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Filament\Resources\Orders\Schemas\OrderForm;
use App\Filament\Resources\Orders\Tables\OrdersTable;
use App\Models\Order;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Support\Enums\TextSize;


class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;
    protected static ?string $recordTitleAttribute = 'id';
    protected static ?string $navigationLabel = 'Data Pesanan';

    public static function form(Schema $schema): Schema
    {
        return OrderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrdersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            // 'create' => CreateOrder::route('/create'),
            // 'edit' => EditOrder::route('/{record}/edit'),
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['items.product']);
    }

    public static function infolist(Schema $schema): Schema
    {
        // dd($schema);
         return $schema->schema([

        // 🔹 Info utama (2 kolom)
        TextEntry::make('customer_name')
            ->label('Nama Pelanggan')
            ->weight('bold'),

        TextEntry::make('table_number')
            ->label('No Meja'),

        TextEntry::make('order_number')
            ->label('No Order')
            ->columnSpan(1),

        TextEntry::make('payment_method')
            ->label('Metode Bayar')
            ->badge() // biar jadi label keren
            ->color('success'),

        TextEntry::make('total_price')
            ->label('Total')
            ->money('IDR')
            ->weight('extrabold')
            ->size(TextSize::Large)
            ->color('primary')
            ->columnSpanFull(),

        // 🔥 RINCIAN MENU
        RepeatableEntry::make('items')
            ->label('Rincian Menu')
            ->columns(4)
            ->contained(true) // bikin kayak card
            ->schema([
                TextEntry::make('product.name')
                    ->label('Menu')
                    ->weight('medium'),

                TextEntry::make('quantity')
                    ->label('Jumlah'),

                TextEntry::make('price_at_sale')
                    ->label('Harga')
                    ->money('IDR'),
                TextEntry::make('subtotal')
                    ->label('Subtotal')
                    ->money('IDR')
                    ->getStateUsing(fn ($record) => $record->quantity * $record->price_at_sale)
            ])
            ->columnSpanFull(),

    ])->columns(2); // 🔥 penting: layout 2 kolom atas
    }
}
