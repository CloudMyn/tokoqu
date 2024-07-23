<?php

namespace App\Filament\Resources\StoreDashboard\TransactionBuyResource\Pages;

use App\Filament\Resources\StoreDashboard\TransactionBuyResource;
use App\Filament\Widgets\TrxBuyOverview;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListTransactionBuys extends ListRecords
{
    protected static string $resource = TransactionBuyResource::class;

    protected static ?string $title = 'Tabel Transaksi Pembelian';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Input Pembelian'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TrxBuyOverview::class
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
