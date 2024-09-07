<?php

namespace App\Filament\Widgets;

use App\Models\Debtor;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AssetsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;


    protected static ?int $sort = 6;

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $model_toko     =   get_context_store();

        $scope          =   [now()->startOfMonth(), now()->endOfMonth()];

        $total_asset    =   $model_toko->store_assets()->where('type', 'in')->sum('amount') - $model_toko->store_assets()->where('type', 'out')->sum('amount');

        $q_in   =   $model_toko->store_assets()->whereBetween('created_at', $scope)->where('type', 'in');

        $q_out  =   $model_toko->store_assets()->whereBetween('created_at', $scope)->where('type', 'out');

        $amount  =  $model_toko->debtors()->where('status', '!=', 'paid')->sum('amount');

        $paid  =  $model_toko->debtors()->where('status', '!=', 'paid')->sum('paid');

        $asset_in_this_mounth      =   $q_in->sum('amount');

        $asset_out_this_mounth     =   $q_out->sum('amount');

        $asset_hold_this_mounth     =   $amount - $paid;


        return [

            Stat::make('Total Kas Toko', "Rp. " . ubah_angka_int_ke_rupiah($model_toko->assets))
                ->icon('heroicon-o-banknotes'),

            Stat::make('Kas Masuk ( Bulan Ini )', "Rp. " . ubah_angka_int_ke_rupiah($asset_in_this_mounth))
                ->chart($q_in->pluck('amount')->toArray())
                ->icon('heroicon-o-arrow-trending-up')
                ->color('success'),

            Stat::make('Kas Keluar ( Bulan Ini )', "Rp. " . ubah_angka_int_ke_rupiah($asset_out_this_mounth))
                ->chart($q_out->pluck('amount')->toArray())
                ->icon('heroicon-o-arrow-trending-down')
                ->color('danger'),

            Stat::make('Kas Terpinjam', "Rp. " . ubah_angka_int_ke_rupiah($asset_hold_this_mounth))
                ->chart($q_out->pluck('amount')->toArray())
                ->icon('heroicon-o-question-mark-circle')
                ->color('danger'),

        ];
    }

    public static function canView(): bool
    {
        return cek_store_role() && cek_store_exists();
    }
}
