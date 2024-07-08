<?php

namespace App\Filament\Exports;

use App\Models\Product;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ProductExporter extends Exporter
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')->label('Nama'),
            ExportColumn::make('image')->label('Foto'),
            ExportColumn::make('sku')->label('SKU'),
            ExportColumn::make('sale_price')->label('Harga Jual'),
            ExportColumn::make('product_cost')->label('Harga Beli'),
            ExportColumn::make('store_code')->label('Kode Toko'),
            ExportColumn::make('stock')->label('Stok'),
            ExportColumn::make('fraction')->label('Fraksi'),
            ExportColumn::make('unit')->label('Unit'),
            ExportColumn::make('supplier')->label('Supplier'),
            ExportColumn::make('created_at')->label('Dibuat pada'),
            ExportColumn::make('updated_at')->label('Diperbarui pada'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your product export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
