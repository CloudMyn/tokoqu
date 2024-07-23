<?php

namespace App\Filament\Exports;

use App\Models\TransactionBuy;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class TransactionBuyExporter extends Exporter
{
    protected static ?string $model = TransactionBuy::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('image')
                ->label('Gambar'),
            ExportColumn::make('store_code')
                ->label('Kode Toko'),
            ExportColumn::make('title')
                ->label('Judul'),
            ExportColumn::make('supplier')
                ->label('Penyedia'),
            ExportColumn::make('total_cost')
                ->label('Total Harga'),
            ExportColumn::make('total_qty')
                ->label('Total Qty'),
            ExportColumn::make('admin_id')
                ->label('Admin ID'),
            ExportColumn::make('admin_name')
                ->label('Nama Admin'),
            ExportColumn::make('created_at')
                ->label('Dibuat Pada'),
            ExportColumn::make('updated_at')
                ->label('Terakhir Diperbarui'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Ekspor transaksi pembelian Anda telah selesai dan ' . number_format($export->successful_rows) . ' ' . str('baris')->plural($export->successful_rows) . ' ekspor.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('baris')->plural($failedRowsCount) . ' gagal untuk ekspor.';
        }

        return $body;
    }
}
