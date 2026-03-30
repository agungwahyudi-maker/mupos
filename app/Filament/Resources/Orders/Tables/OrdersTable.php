<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\InfoList;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;


class OrdersTable
{
    
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer_name')
                    ->Label('Nama Pelanggan')
                    ->searchable(),
                TextColumn::make('table_number')
                    ->Label('Nomor Meja')
                    ->searchable(),
                TextColumn::make('order_number')
                    ->Label('Nomor Pesanan')
                    ->searchable(),
                TextColumn::make('total_price')
                    ->money()
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                // ViewAction::make()
                //     ->label('Lihat Detail'),
                ViewAction::make()
                    ->label('Lihat Detail')
                    ->mutateRecordDataUsing(function ($record) {
                        $record->loadMissing('items.product');

                        return $record->toArray(); // ✅ HARUS array
                    }),
                Action::make('print')
                    ->label('Cetak')
                    ->url(fn ($record) => route('order.print', $record))
                    ->openUrlInNewTab(),
                // EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
    
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Section Informasi Order (Yang sudah ada di gambar Anda)
                Section::make('Informasi Pesanan')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('customer_name')->label('Nama Pelanggan'),
                            TextEntry::make('table_number')->label('Nomor Meja'),
                            TextEntry::make('order_number')->label('Nomor Invoice'),
                            TextEntry::make('total_price')->money('IDR')->label('Total Harga'),
                        ]),
                    ]),
                
                    

                // TAMBAHKAN SECTION INI UNTUK MENAMPILKAN ITEM
                Section::make('Daftar Menu yang Dipesan')
                    ->schema([
                        TextEntry::make('debug_items')
                            ->label('DEBUG ITEMS')
                            ->state(fn ($record) => $record->items?->toArray()),
                        RepeatableEntry::make('items') // Nama relasi di Model Order
                            ->label('')
                            ->schema([
                                TextEntry::make('id'),
                                Grid::make(4)
                                    ->schema([
                                        TextEntry::make('product.name') // Mengambil nama produk dari relasi
                                            ->label('Menu'),
                                        TextEntry::make('quantity')
                                            ->label('Jumlah')
                                            ->suffix('x'),
                                        TextEntry::make('price_at_sale')
                                            ->label('Harga Satuan')
                                            ->money('IDR'),
                                        TextEntry::make('subtotal')
                                            ->label('Subtotal')
                                            // ->state(fn ($record) => $record->quantity * $record->price_at_sale)
                                            ->state(fn ($state) => $state['quantity'] * $state['price_at_sale'])
                                            ->money('IDR'),
                                    ]),
                            ])
                    ]),
                    Section::make('Ringkasan')
                        ->schema([
                            TextEntry::make('total_price')
                                ->label('Total')
                                ->money('IDR')
                                ->size('lg')
                                ->weight('bold'),
                        ])
            ]);
    }
}
