<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProductsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $model_toko     =   get_context_store();

        $base_query     =   $model_toko->products();

        return [
            Stat::make('Jumlah Produk', $base_query->count()),

            Stat::make('QTY Stock', ubah_angka_int_ke_rupiah($base_query->sum('stock'))),

            Stat::make('Nilai Stock', "Rp. " . ubah_angka_int_ke_rupiah($base_query->where('stock', '>', 0)->sum('product_cost') * $base_query->sum('stock'))),
        ];
    }

    public static function canView(): bool
    {
        return cek_store_role();
    }
}
