<?php

namespace App\Filament\Resources\StoreDashboard\TransactionSaleResource\Pages;

use App\Filament\Resources\StoreDashboard\TransactionSaleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Widgets\TrxSaleOverview;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListTransactionSales extends ListRecords
{
    protected static string $resource = TransactionSaleResource::class;

    protected static ?string $title = 'Tabel Transaksi Penjualan';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Input Penjualan'),
        ];
    }


    protected function getHeaderWidgets(): array
    {
        return [
            TrxSaleOverview::class
        ];
    }

    public function getTabs(): array
    {
        return [
            'Semua' => Tab::make(),
            'Hari Ini' => Tab::make()->modifyQueryUsing(function (Builder $query) {
                session()->put('active_tab', 'Hari Ini');
                return $query->whereDate('created_at', today());
            }),
            'Minggu Ini' => Tab::make()->modifyQueryUsing(function (Builder $query) {
                session()->put('active_tab', 'Minggu Ini');
                return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            }),
            'Bulan Ini' => Tab::make()->modifyQueryUsing(function (Builder $query) {
                session()->put('active_tab', 'Bulan Ini');
                return $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
            }),
            'Tahun Ini' => Tab::make()->modifyQueryUsing(function (Builder $query) {
                session()->put('active_tab', 'Tahun Ini');
                return $query->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()]);
            }),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'Semua';
    }
}
