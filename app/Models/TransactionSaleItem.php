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

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($trx_item) {
            $product = $trx_item->product;

            $product->update([
                'stock' =>  $product->stock + intval($trx_item->total_qty),
            ]);
        });
    }

    public function transaction()
    {
        return $this->belongsTo(TransactionBuy::class, 'transaction_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_code', 'code');
    }

    public function sale_product(Product $product, int $qty, int $discount): void
    {
        if ($product->qty > $qty) {
            throw new \Exception('Stock Barang #' . $product->sku .  ' Tidak Cukup');
        }

        if ($qty <= 0) {
            throw new \Exception('Qty minimal 1');
        }

        $sale_price     =   doubleval($product->sale_price);
        $product_cost   =   doubleval($product->product_cost);

        $gross_sale     =   ($sale_price - $discount) * $qty;

        $profit         =   $gross_sale - ($product_cost * $qty);

        $this->sale_price      =   $gross_sale;
        $this->sale_profit     =   $profit;

        $product->update([
            'stock' => $product->stock - $qty
        ]);
    }
}
