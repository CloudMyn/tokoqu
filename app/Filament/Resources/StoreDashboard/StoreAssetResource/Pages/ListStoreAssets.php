<?php

namespace App\Filament\Resources\Store\StoreResource\StoreAssetResource\Pages;

use App\Filament\Resources\StoreDashboard\StoreAssetResource;
use App\Filament\Resources\StoreDashboard\TransactionSaleResource\Widgets\TrxSaleChart;
use App\Filament\Widgets\AssetsOverview;
use Filament\Actions;
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
            Actions\CreateAction::make()->label('Input Kas'),
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
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'in')),
            'Kas Keluar' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'out')),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'semua';
    }
}
