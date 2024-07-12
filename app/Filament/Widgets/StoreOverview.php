<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StoreOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $model_toko   =   get_context_store();

        $trx_sales      =   $model_toko->transaction_sales()->sum('total_amount');

        $sales_profit   =   $model_toko->transaction_sales()->sum('total_profit');

        return [
            Stat::make('Jumlah Asset', "Rp. " . ubah_angka_int_ke_rupiah($model_toko->assets)),
            Stat::make('Total Transaksi Penjualan ( Bulan Ini )', "Rp. " . ubah_angka_int_ke_rupiah($trx_sales)),
            Stat::make('Total Keuntungan Penjualan ( Bulan Ini )', "Rp. " . ubah_angka_int_ke_rupiah($sales_profit)),
        ];
    }

    public static function canView(): bool
    {
        return cek_store_role();
    }
}
