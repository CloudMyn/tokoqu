<?php

namespace App\Filament\Imports;

use App\Models\Product;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ProductImporter extends Importer
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:100']),
            ImportColumn::make('image')
                ->rules(['max:255']),
            ImportColumn::make('sku')
                ->label('SKU')
                ->requiredMapping()
                ->rules(['required', 'max:6']),
            ImportColumn::make('sale_price')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer', 'min:1']),
            ImportColumn::make('product_cost')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer', 'min:1']),
            ImportColumn::make('stock')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer', 'min:0']),
            ImportColumn::make('fraction')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer', 'min:1', 'max:99999999']),
            ImportColumn::make('unit')
                ->requiredMapping()
                ->rules(['required', 'in:carton,pack,piece,box,bag,set,bottle,jar,roll,case,pallet,bundle,liter,milliliter,kilogram,gram,other']),
            ImportColumn::make('supplier')
                ->rules(['max:100']),
        ];
    }

    public function resolveRecord(): ?Product
    {
        // return Product::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Product();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Impor produk Anda telah selesai dan ' . number_format($import->successful_rows) . ' ' . str('baris')->plural($import->successful_rows) . ' diimpor.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('baris')->plural($failedRowsCount) . ' gagal untuk diimpor.';
        }

        return $body;
    }
}
