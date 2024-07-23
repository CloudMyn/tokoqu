<?php

namespace App\Filament\Exports;

use App\Models\StoreAsset;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class StoreAssetExporter extends Exporter
{
    protected static ?string $model = StoreAsset::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID (Indonesia)'),
            ExportColumn::make('title')
                ->label('Judul (Indonesia)'),
            ExportColumn::make('message')
                ->label('Pesan (Indonesia)'),
            ExportColumn::make('amount')
                ->label('Jumlah (Indonesia)'),
            ExportColumn::make('type')
                ->label('Tipe (Indonesia)'),
            ExportColumn::make('store_code')
                ->label('Kode Toko (Indonesia)'),
            ExportColumn::make('created_at')
                ->label('Dibuat pada (Indonesia)'),
            ExportColumn::make('updated_at')
                ->label('Diperbarui pada (Indonesia)'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Ekspor kas toko Anda telah selesai dan ' . number_format($export->successful_rows) . ' ' . str('baris')->plural($export->successful_rows) . ' diekspor.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('baris')->plural($failedRowsCount) . ' gagal diekspor.';
        }

        return $body;
    }
}
