<?php

namespace App\Filament\Resources\StoreDashboard\ProductResource\Pages;

use App\Filament\Exports\ProductExporter;
use App\Filament\Resources\StoreDashboard\ProductResource;
use Filament\Actions;
use Filament\Actions\ExportAction;
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
}
