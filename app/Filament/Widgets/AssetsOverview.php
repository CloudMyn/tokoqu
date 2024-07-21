<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AssetsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $model_toko   =   get_context_store();

        $scope          =   [now()->startOfMonth(), now()->endOfMonth()];

        $asset_in      =   $model_toko->store_assets()->where('type', 'in')->sum('amount');

        $asset_in_this_mounth       =   $model_toko->store_assets()->whereBetween('created_at', $scope)->where('type', 'in')->sum('amount');

        $asset_out_this_mounth     =   $model_toko->store_assets()->whereBetween('created_at', $scope)->where('type', 'out')->sum('amount');

        return [
            Stat::make('Total Kas Toko', "Rp. " . ubah_angka_int_ke_rupiah($asset_in)),
            Stat::make('Pemasukan Bulan Ini', "Rp. " . ubah_angka_int_ke_rupiah($asset_in_this_mounth)),
            Stat::make('Pengeluaran Bulan Ini', "Rp. " . ubah_angka_int_ke_rupiah($asset_out_this_mounth)),
        ];
    }

    public static function canView(): bool
    {
        return cek_store_role();
    }
}
