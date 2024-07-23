<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdjustOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected static ?int $sort = 7;

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $model_toko   =   get_context_store();

        $scope          =   [now()->startOfYear(), now()->endOfYear()];

        $query_in   =   $model_toko->adjusts_stock()->whereBetween('created_at', $scope)->where('type', 'plus');

        $query_out  =   $model_toko->adjusts_stock()->whereBetween('created_at', $scope)->where('type', 'minus');

        $adjust_in_this_mounth      =   $query_in->sum('total_amount');

        $adjust_out_this_mounth     =   $query_out->sum('total_amount');

        return [
            Stat::make('Total Adjust ( Tahun Ini )', $model_toko->adjusts_stock()->whereBetween('created_at', $scope)->count()),

            Stat::make('Adjust Plus ( Tahun Ini )', "Rp. " . ubah_angka_int_ke_rupiah($adjust_in_this_mounth))
                ->chart($query_in->pluck('total_amount')->toArray())
                ->color('success'),

            Stat::make('Adjust Minus ( Tahun Ini )', "Rp. " . ubah_angka_int_ke_rupiah($adjust_out_this_mounth))
                ->chart($query_out->pluck('total_amount')->toArray())
                ->color('danger'),
        ];
    }

    public static function canView(): bool
    {
        return cek_store_role() && cek_store_exists();
    }
}
