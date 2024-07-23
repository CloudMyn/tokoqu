<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TrxOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $model_toko   =   get_context_store();

        $scope          =   [now()->startOfMonth(), now()->endOfMonth()];

        $trx_sales      =   $model_toko->transaction_sales()->whereBetween('created_at', $scope)->sum('total_amount');

        $sales_profit   =   $model_toko->transaction_sales()->whereBetween('created_at', $scope)->sum('total_profit');

        $trx_buy        =   $model_toko->transaction_buys()->whereBetween('created_at', $scope)->sum('total_cost');

        return [
            Stat::make('Total Transaksi Penjualan ( Bulan Ini )', "Rp. " . ubah_angka_int_ke_rupiah($trx_sales)),
            Stat::make('Total Keuntungan Penjualan ( Bulan Ini )', "Rp. " . ubah_angka_int_ke_rupiah($sales_profit)),
            Stat::make('Total Pembelian Barang ( Bulan Ini )', "Rp. " . ubah_angka_int_ke_rupiah($trx_buy)),
        ];
    }

    public static function canView(): bool
    {
        return cek_store_role() && cek_store_exists();
    }
}
