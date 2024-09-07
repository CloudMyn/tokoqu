<?php

namespace App\Filament\Resources\DebtorResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DebtsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $store  =   get_context_store();

        $total_all      =   $store->debtors()->sum('amount');

        $total_paid     =   $store->debtors()->sum('paid');

        return [
            Stat::make('Total Penghutang', $store->debtors()->count())
                ->icon('heroicon-o-user-group'),
            Stat::make('Telat Bayar', $store->debtors()->where('status', 'overdue')->count())
                ->icon('heroicon-o-user-group'),
            Stat::make('Total Keseluruhan', "RP. " .  ubah_angka_int_ke_rupiah($total_all))
                ->icon('heroicon-o-banknotes'),
            Stat::make('Total Terbayarkan', "RP. " . ubah_angka_int_ke_rupiah($total_paid))
                ->icon('heroicon-o-banknotes'),
        ];
    }
}
