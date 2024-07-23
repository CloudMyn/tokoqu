<?php

namespace App\Filament\Exports;

use App\Models\TransactionSale;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class TransactionSaleExporter extends Exporter
{
    protected static ?string $model = TransactionSale::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID (Indonesia)'),
            ExportColumn::make('image')
                ->label('Gambar'),
            ExportColumn::make('title')
                ->label('Judul'),
            ExportColumn::make('total_amount')
                ->label('Total Harga'),
            ExportColumn::make('total_qty')
                ->label('Total Jumlah'),
            ExportColumn::make('total_profit')
                ->label('Total Keuntungan'),
            ExportColumn::make('admin_id')
                ->label('ID Admin'),
            ExportColumn::make('admin_name')
                ->label('Nama Admin'),
            ExportColumn::make('store_code')
                ->label('Kode Toko'),
            ExportColumn::make('created_at')
                ->label('Dibuat pada'),
            ExportColumn::make('updated_at')
                ->label('Diperbarui pada'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Ekspor transaksi penjualan Anda telah selesai dan ' . number_format($export->successful_rows) . ' ' . str('baris')->plural($export->successful_rows) . ' berhasil diekspor.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('baris')->plural($failedRowsCount) . ' gagal diekspor.';
        }

        return $body;
    }
}
