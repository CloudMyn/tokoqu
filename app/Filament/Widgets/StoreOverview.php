<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StoreOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected static ?int $sort = -2;

    protected int | string | array $columnSpan = [
        'sm'    =>  1,
        'md'    =>  2,
        'xl'    =>  2,
    ];

    protected function getStats(): array
    {
        $model_toko   =   get_context_store();

        return [
            Stat::make('Kas Toko', "Rp. " . ubah_angka_int_ke_rupiah($model_toko->assets)),
            Stat::make('Jumlah Produk', $model_toko->products()->count() . " Produk"),
            Stat::make('Jumlah Karyawan', $model_toko->employees()->count() . " Orang"),
        ];
    }

    public static function canView(): bool
    {
        return cek_store_role() && cek_store_exists();
    }
}
