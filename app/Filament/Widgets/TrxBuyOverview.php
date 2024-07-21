<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TrxBuyOverview extends BaseWidget
{
    protected static ?string $pollingInterval = "10s";

    protected function getStats(): array
    {
        $model_toko   =   get_context_store();

        $scope_ctx      =   session('active_tab', 'Semua');

        session()->forget('active_tab');

        $scope          =   match ($scope_ctx) {
            'Semua' => [],
            'Hari Ini' => [now()->startOfDay(), now()->endOfDay()],
            'Minggu Ini' => [now()->startOfWeek(), now()->endOfWeek()],
            'Bulan Ini' => [now()->startOfMonth(), now()->endOfMonth()],
            'Tahun Ini' => [now()->startOfYear(), now()->endOfYear()],
        };

        $base_query     =   $model_toko->transaction_sales()->whereBetween('created_at', $scope);

        if ($scope_ctx == 'Semua') $base_query     =   $model_toko->transaction_sales();

        $total_trxt     =   $base_query->sum('total_amount');

        $total_prv      =   $base_query->sum('total_profit');

        return [
            Stat::make('Jumlah Transaksi', $base_query->count()),

            Stat::make('Total Transaksi', "Rp. " . ubah_angka_int_ke_rupiah($total_trxt))
                ->chart($base_query->pluck('total_amount')->toArray())
                ->color('success'),

            Stat::make('Total Keuntungan', "Rp. " . ubah_angka_int_ke_rupiah($total_prv))
                ->chart($base_query->pluck('total_profit')->toArray())
                ->color('success'),
        ];
    }

    public static function canView(): bool
    {
        return cek_store_role();
    }
}
