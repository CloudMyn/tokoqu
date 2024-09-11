<?php

namespace App\Filament\Resources\StoreDashboard\ProductResource\Pages;

use App\Filament\Resources\StoreDashboard\ProductResource;
use App\Models\Product;
use App\Models\TransactionSaleItem;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProduct extends ViewRecord
{
    protected static string $resource = ProductResource::class;

    protected static string $view = 'dashboard.product.view';

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $product    =   Product::find($record);

        $trx_items  =   $product->transaction_sale_items()->orderBy('created_at', 'DESC')->take(10)->get();

        $product->transaction_sale_items    =   $trx_items;

        $start_date = now()->startOfMonth();
        $end_date   = now()->endOfMonth();

        $monthly_report =   $this->produce_report($product, $start_date, $end_date);

        $yearly_report  =   $this->produce_report($product, now()->startOfYear(), now()->endOfYear());

        $product->monthly_report    =   $monthly_report;

        $product->yearly_report     =   $yearly_report;

        $product->product_suppliers =   $product->product_suppliers()->orderBy('price', 'ASC')->get();

        $this->getInfolist('infolist')->record($product);
    }

    private function produce_report(Product $product, $start_date, $end_date)
    {
        $query_builder  =   $product->transaction_sale_items()->whereBetween('created_at', [$start_date, $end_date]);

        $data_repot['TOTAL KEUTUNGAN PENJUALAN']    =   "Rp. " . ubah_angka_int_ke_rupiah($query_builder->sum('sale_profit'));
        $data_repot['AVG KEUTUNGAN PENJUALAN']      =   "Rp. " . ubah_angka_int_ke_rupiah($query_builder->avg('sale_profit'));
        $data_repot['TOTAL TRANSAKSI PENJUALAN']    =   "Rp. " . ubah_angka_int_ke_rupiah($query_builder->sum('sale_price'));
        $data_repot['AVG TRANSAKSI PENJUALAN']      =   "Rp. " . ubah_angka_int_ke_rupiah($query_builder->avg('sale_price'));
        $data_repot['TOTAL QTY PENJUALAN']          =   $query_builder->sum('total_qty');
        $data_repot['TOTAL QTY (IN UNIT)']          =   round($query_builder->sum('total_qty') / $product->fraction) . " " . strtoupper($product->unit);

        return $data_repot;
    }
}
