<?php

namespace App\Filament\Resources\StoreDashboard\TransactionBuyResource\Pages;

use App\Filament\Exports\TransactionBuyExporter;
use App\Filament\Resources\StoreDashboard\TransactionBuyResource;
use App\Filament\Widgets\TrxBuyOverview;
use Filament\Actions;
use Filament\Actions\ActionGroup;
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


            ActionGroup::make([

                Actions\Action::make('export_laporan')
                    ->label('Laporan Pembelian')
                    ->icon('heroicon-o-document-text')
                    ->url(route('report.buy'), true),

                Actions\ExportAction::make()
                    ->exporter(TransactionBuyExporter::class)
                    ->modifyQueryUsing(fn(Builder $query) => $query->where('store_code', get_context_store()->code))
                    ->icon('heroicon-o-document-chart-bar')
                    ->label('Eksport Data'),

            ])
                ->label('Laporan')
                ->icon('heroicon-o-arrow-up-on-square-stack')
                ->color('info')
                ->button()
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
