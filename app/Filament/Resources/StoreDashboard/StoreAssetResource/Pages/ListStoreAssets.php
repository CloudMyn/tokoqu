<?php

namespace App\Filament\Resources\StoreDashboard\StoreAssetResource\Pages;

use App\Filament\Exports\StoreAssetExporter;
use App\Filament\Resources\StoreDashboard\StoreAssetResource;
use App\Filament\Resources\StoreDashboard\TransactionSaleResource\Widgets\TrxSaleChart;
use App\Filament\Widgets\AssetsOverview;
use Filament\Actions;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ListRecords;

class ListStoreAssets extends ListRecords
{
    protected static string $resource = StoreAssetResource::class;

    protected static ?string $title = 'Kas Toko';

    protected function getHeaderActions(): array
    {
        return [

            Actions\Action::make('sync_assets')
                ->label('Sinkronisasi Kas')
                ->icon('heroicon-o-arrow-path')
                ->action(function () {
                    sync_store_assets();

                    Notification::make()
                        ->success()
                        ->title('Sinkroniasasi Berhasil!')
                        ->body('Kas toko telah disinkronkan.')
                        ->send();
                }),

            Actions\CreateAction::make()
                ->icon('heroicon-o-document-plus')
                ->label('Input Kas'),


            ActionGroup::make([

                Actions\Action::make('export_laporan')
                    ->label('Laporan Kas Toko')
                    ->icon('heroicon-o-document-text')
                    ->url(route('report.assets'), true),

                Actions\ExportAction::make()
                    ->exporter(StoreAssetExporter::class)
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
            AssetsOverview::class
        ];
    }

    public function getTabs(): array
    {
        return [
            'Semua' => Tab::make(),
            'Kas Masuk' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('type', 'in')),
            'Kas Keluar' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('type', 'out')),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'semua';
    }
}
