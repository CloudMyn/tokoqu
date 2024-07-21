<?php

namespace App\Filament\Resources\StoreDashboard\ProductResource\Pages;

use App\Filament\Exports\ProductExporter;
use App\Filament\Resources\StoreDashboard\ProductResource;
use App\Filament\Widgets\ProductsOverview;
use Filament\Actions;
use Filament\Actions\ExportAction;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected static ?string $title = 'Daftar Produk';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Produk'),
            ExportAction::make()
                ->exporter(ProductExporter::class)
                ->label('Eksport Produk'),
        ];
    }


    protected function getHeaderWidgets(): array
    {
        return [
            ProductsOverview::class
        ];
    }

    public function getTabs(): array
    {
        return [
            'Semua' => Tab::make(),
            'Ada Stock' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('stock', '>', 0)),
            'Stock Kosong' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('stock', 0)),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'Semua';
    }
}
