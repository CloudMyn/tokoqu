<?php

namespace App\Filament\Exports;

use App\Models\Debtor;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class DebtorExporter extends Exporter
{
    protected static ?string $model = Debtor::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('name')
                ->label('Nama'),
            ExportColumn::make('phone')
                ->label('Telepon'),
            ExportColumn::make('amount')
                ->label('Jumlah'),
            ExportColumn::make('paid')
                ->label('Terbayar'),
            ExportColumn::make('transaction_id')
                ->label('ID Transaksi'),
            ExportColumn::make('asset_id')
                ->label('ID Aset'),
            ExportColumn::make('status')
                ->label('Status'),
            ExportColumn::make('due_date')
                ->label('Tanggal Jatuh Tempo'),
            ExportColumn::make('note')
                ->label('Catatan'),
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
        $body = 'Ekspor data Anda telah selesai dan ' . number_format($export->successful_rows) . ' ' . str('baris')->plural($export->successful_rows) . ' diekspor.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('baris')->plural($failedRowsCount) . ' gagal diekspor.';
        }

        return $body;
    }
}
