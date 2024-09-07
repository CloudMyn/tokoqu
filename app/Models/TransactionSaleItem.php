<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionSaleItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'total_qty'     =>  'integer',
        'sale_price'    =>  'double',
        'sale_profit'   =>  'double'
    ];

    protected $with =   [
        'product',
        'transaction'
    ];

    protected static function booted()
    {
        static::deleting(function ($trx_item) {
            $product = $trx_item->product;

            $product->update([
                'stock' =>  $product->stock + intval($trx_item->total_qty),
            ]);
        });
    }

    public function transaction()
    {
        return $this->belongsTo(TransactionSale::class, 'transaction_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_code', 'code');
    }

    public function sale_product(Product $product, int $qty, int $discount, int $discount_per_qty, int $delivery_fee = 0): void
    {
        if ($product->stock < $qty) {
            throw new \Exception('Stock Barang #' . $product->sku .  ' Tidak Cukup');
        }

        if ($qty <= 0) {
            throw new \Exception('Qty minimal 1');
        }

        $potongan_per_product   =   ubah_angka_rupiah_ke_int($discount ?? 0);

        $potongan_per_qty       =   ubah_angka_rupiah_ke_int($discount_per_qty ?? 0) * $qty;

        $gross_trx      =   (($product->sale_price ?? 0) * $qty - $potongan_per_product) - $potongan_per_qty + $delivery_fee;

        $profit         =   $gross_trx - ($product->product_cost ?? 0) * $qty;

        $this->sale_price      =   $gross_trx;
        $this->sale_profit     =   $profit;

        $product->update([
            'stock' => $product->stock - $qty
        ]);
    }
}
