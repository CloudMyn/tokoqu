<?php

namespace App\Filament\Resources\Store\StoreResource\StoreAssetResource\Pages;

use App\Filament\Resources\StoreDashboard\StoreAssetResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ListRecords;

class ListStoreAssets extends ListRecords
{
    protected static string $resource = StoreAssetResource::class;

    protected static ?string $title = 'Daftar Kas';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Input Kas'),
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
