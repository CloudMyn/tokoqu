<?php

namespace App\Filament\Exports;

use App\Models\AdjustStock;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class AdjustStockExporter extends Exporter
{
    protected static ?string $model = AdjustStock::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('message')
                ->label('Pesan'),
            ExportColumn::make('total_amount')
                ->label('Total Amount'),
            ExportColumn::make('total_qty')
                ->label('Total Qty'),
            ExportColumn::make('type')
                ->label('Tipe'),
            ExportColumn::make('admin_name')
                ->label('Nama Admin'),
            ExportColumn::make('admin_id')
                ->label('ID Admin'),
            ExportColumn::make('product_id')
                ->label('ID Produk'),
            ExportColumn::make('store_code')
                ->label('Kode Toko'),
            ExportColumn::make('created_at')
                ->label('Dibuat Pada'),
            ExportColumn::make('updated_at')
                ->label('Diperbarui Pada'),
        ];
    }
    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Ekspor stok adjust Anda telah selesai dan ' . number_format($export->successful_rows) . ' ' . str('baris')->plural($export->successful_rows) . ' berhasil diekspor.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('baris')->plural($failedRowsCount) . ' gagal diekspor.';
        }

        return $body;
    }
}
